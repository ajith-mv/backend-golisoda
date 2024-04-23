<?php
namespace App\Helpers;

use App\Models\User;

class AccessGuard {
    public function hasAccess($module, $permission_module = '') {
        if( auth()->user()->is_super_admin ) {
            return true;
        } else {
            $info = User::find(auth()->id());
            $data = $info->role->permissions;
            $data = unserialize($data);
            if( is_string( $module ) ) {
                $module = array( $module );
            }
            
            if( isset( $module ) && !empty( $module ) && is_array( $module ) ) {
                foreach( $module as $item ) {
                    if( !empty($data)) {
                        if( isset( $data[$item]) && !empty($item)) {
                            if( !empty( $permission_module ) ) {
                                if( isset( $data[$item][$item.'_'.$permission_module] ) && $data[$item][$item.'_'.$permission_module] == 'on') {
                                    return true;
                                } 
                            }
                            if( !empty( $item ) && empty( $permission_module)) {
                                if( isset( $data[$item])) {
                                    if(array_search('on', $data[$item]) ) {
                                        return true;
                                    }
                                }
                            }
                            
                        }
                    }
                }
                
            }
            
            return false;
        }   
    }

    function check_access($permission_module = '') {
        
        $module = request()->route()->getName(); 
        
        $info = User::find(auth()->id());
        $data = $info->role->permissions;
        $data = unserialize($data);
        
        $module = explode(".", $module);
        $module = current($module);
        
        if( !empty($data)) {
            if( isset( $data[$module]) && !empty($module)) {
                
                if( !empty( $permission_module ) ) {
                    if( isset( $data[$module][$module.'_'.$permission_module] ) && $data[$module][$module.'_'.$permission_module] == 'on') {
                        return true;
                    } else {
                        abort(403);
                    }
                }
                if( isset( $data[$module])) {
                    if(array_search('on', $data[$module]) ) {
                        return true;
                    }
                }
                abort(403);
            }
        }
        abort(403);
    }
}