<?php
define('UPLOADS_ROOT_DIR',realpath('../../../'));
define('ADMIN_ROOT_DIR',realpath('../../'));
include ADMIN_ROOT_DIR."/session.php";
//print_r($db); die();

if(isset($_REQUEST['dc_no']) && !empty($_REQUEST['dc_no'])){
$dc_no=$_REQUEST['dc_no'];

$chk_pdf_path = UPLOADS_ROOT_DIR."/uploads/invoice_pdf/".$_REQUEST['dc_no']."_invoice.pdf";


$str_query = " select  t2.dc_no, t2.dc_date, t2.purchase_order_no, t2.purchase_date, t2.purchase_order_img, t2.terms_of_payment, t2.other_reference, t2.transport, t2.vehicle_no, t2.bdm, t2.gm, t2.driver_name, t2.driver_phone, t2.product, t2.cus_name, t2.cus_tin_no, t2.cus_cst_no, t2.cus_address, t2.cus_delivery_address, t3.cus_state, t1.product_id, t4.product_name, t1.grade_type, t1.qty, t1.rate_given, t1.rate_actual as rate, t1.uom, t1.actual_amount, t1.vat_tax,  t1.vat_tax_amount, t1.total_amount, t2.cst_tax, t2.cst_tax_amount, t2.overall_tot_amount, DATE_FORMAT(t2.dc_date,'%d-%b-%Y') AS dispdc_date, DATE_FORMAT(t2.purchase_date,'%Y-%m-%d') AS disp_purchase_date, DATE_FORMAT(t2.createddate,'%d-%b-%Y') AS disp_dc_create_date, t2.batch_no, t5.user_firstname as disp_created_by, t2.IsActive as disp_status, t6.user_firstname as disp_delete_by 	from m_deliverychallan_details t1 
inner join m_deliverychallan t2 on t2.dc_no = t1.dc_no
inner join m_customers t3 on alphanum(t2.cus_name) = alphanum(t3.cus_name) 
inner join m_products t4 on t1.product_id = t4.product_id 
inner join a_users t5 on t2.UserId = t5.user_ID  
inner join a_users t6 on t1.UserId = t6.user_ID 
where 1=1 and t1.IsActive=1 and t2.IsActive=1 and t2.dc_no='".$dc_no."' ";

$rslt_data = $db->get_a_line($str_query);


$body_html ='<html>
<head>
<title>Riyara Invoice</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>

<table width="60%" cellspacing="0" cellpadding="3" bordercolor="#FF4D4D" border="1">

  <tbody><tr>

    <td><table height="144" width="100%" cellspacing="0" cellpadding="5" border="0">

      <tbody><tr bgcolor="#FF4D4D">

        <td height="24" valign="top" colspan="3"><font face="Arial, Helvetica, sans-serif" size="4" color="#ffffff">Invoice Details</font></td>

      </tr>	  	    

      <tr>

        <td height="24" width="24%" valign="top"><font face="Arial, Helvetica, sans-serif" size="2" color="#000d5a"><b>Dc No</b></font></td>

        <td width="2%" valign="top">: </td>

        <td width="74%" valign="top"><font face="Arial, Helvetica, sans-serif" size="2" color="#000d5a">'.$rslt_data['dc_no'].'</font></td>

      </tr>
	  
	  <tr>

        <td height="24" valign="top"><font face="Arial, Helvetica, sans-serif" size="2" color="#000d5a"><b>Dc Date</b></font></td>

        <td valign="top">: </td>

        <td valign="top"><font face="Arial, Helvetica, sans-serif" size="2" color="#000d5a">'.$rslt_data['dispdc_date'].'</font></td>

      </tr>
	  
	  <tr>
        <td height="24" valign="top"><font face="Arial, Helvetica, sans-serif" size="2" color="#000d5a"><b>Created By</b></font></td>

        <td valign="top">: </td>

        <td valign="top"><font face="Arial, Helvetica, sans-serif" size="2" color="#000d5a">'.$rslt_data['disp_created_by'].'</font></td>

      </tr>
	  
	   <tr>
        <td height="24" valign="top"><font face="Arial, Helvetica, sans-serif" size="2" color="#000d5a"><b>Created Date</b></font></td>

        <td valign="top">: </td>

        <td valign="top"><font face="Arial, Helvetica, sans-serif" size="2" color="#000d5a">'.$rslt_data['disp_dc_create_date'].'</font></td>

      </tr>
	  	    
    </tbody></table></td>

  </tr>

</tbody></table>

</body>
</html>';


//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

require '../PHPMailerAutoload.php';

//Create a new PHPMailer instance
$mail = new PHPMailer;
$mail_inv = new PHPMailer;

$mail->isSMTP();
$mail_inv->isSMTP();

$mail->SMTPDebug = 0;
$mail_inv->SMTPDebug = 0;

$mail->Debugoutput = 'html';
$mail_inv->Debugoutput = 'html';

$mail->Host = "smtp.gmail.com";
$mail_inv->Host = "smtp.gmail.com";

$mail->Port = 587;
$mail_inv->Port = 587;

$mail->SMTPAuth = true;
$mail_inv->SMTPAuth = true;

$mail->Username = "contactriyara@gmail.com";
$mail_inv->Username = "contactriyara@gmail.com";

$mail->Password = "contactriyara@123";
$mail_inv->Password = "contactriyara@123";

$mail->setFrom('contactriyara@gmail.com', 'New DC Generated');
$mail_inv->setFrom('contactriyara@gmail.com', 'New DC Generated');

$mail->addReplyTo('contactriyara@gmail.com', 'riyara trading');
$mail_inv->addReplyTo('contactriyara@gmail.com', 'riyara trading');

$mail->Subject = 'Dc - '.$dc_no." / ".$rslt_data['dispdc_date']." / ".$rslt_data['cus_name'];
$mail_inv->Subject = 'Dc - '.$dc_no." / ".$rslt_data['dispdc_date']." / ".$rslt_data['cus_name'];

$mail->msgHTML($body_html);
$mail_inv->msgHTML($body_html);

$mail->AltBody = 'New DC Generated';
$mail_inv->AltBody = 'New DC Generated';

//tis send both invoice and purchase order image
$mail->addAddress('pravin@pixel-studios.com', 'Pravin');
$mail->addAddress('john@pixel-studios.com', 'Pravin');

$mail_inv->addAddress('nagarajbabu1988@gmail.com', 'Nagarajbabu');
$mail_inv->addAddress('venkatesh.pixel@gmail.com', 'Venkat');


//Attach an image file

//invoice attachment
if(file_exists($chk_pdf_path)){	
	$chk_pdf_path = str_replace('\\', '/', $chk_pdf_path);
	$mail->addAttachment($chk_pdf_path);
	$mail_inv->addAttachment($chk_pdf_path);
}

//purchase order attachment
$chk_po_path = UPLOADS_ROOT_DIR."/uploads/purchase_order/".$rslt_data['purchase_order_img'];
if(file_exists($chk_po_path)){	
	$chk_po_path = str_replace('\\', '/', $chk_po_path);
	$mail->addAttachment($chk_po_path);
}

$mail->SMTPSecure = 'tls';


	//send the message, check for errors
	if (!$mail->send()) {
		//echo "Mailer Error: " . $mail->ErrorInfo;
		echo json_encode(array("rslt"=>"-1", "error_msg"=>$mail->ErrorInfo)); //error
	} else {  
		$mail_inv->send();
		echo json_encode(array("rslt"=>"1")); //success
	}


}

?>