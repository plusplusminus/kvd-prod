<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link href='https://fonts.googleapis.com/css?family=Work+Sans' rel='stylesheet' type='text/css'><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <style>
    body {
      font-family: 'Work Sans', sans-serif;
      font-size:10px;
    }
    h1,h2,h3,h4,h5,h6{
      font-family: 'Work Sans', sans-serif !important;
    }
    h3{
      font-size: 10px;
    }
    @page { 
		  margin: 480px 50px 100px 50px;
	  } 
    #header { 
  		position: fixed; 
  		left: 0px; 
  		top: -420px; 
  		right: 0px; 
  		height: 0px; 
  		text-align: center;
  	}
    .logo{
      margin: auto;
      display: block;
      margin-bottom: 60px;
    }
    .logo img{
      width: 200px;
      height: auto;
    }
    table{
      border-collapse: collapse; 
      border-spacing: 5px;
    }
    tr.border-bottom td{
      border-bottom: 1px solid #222222;
      padding: 5px 0;
    }
    th{
      padding: 10px 0;
    }
    tr.border-bottom-thick td{
      border-bottom: 1px solid #222222;
      padding: 5px 0;
    }
    .totals{
      border-top: 1px solid #222222;
      border-bottom: 1px solid #222222;
    }
    #footer { 
  		position: fixed; 
  		left: 0px; 
  		bottom: 0px; 
  		right: 0px; 
  		height: 100px; 
  		font-size:8px; 
  		text-align: center;
      width:100%;
  	} 
  	#content { 
  		font-size:10px;
      position: fixed; 
      left: 0px; 
      top: -100px; 
      right: 0px; 
      height: 0px; 
  	}
  </style> 
</head>
  <body> 
  <div id="header"> 
  <table table width="100%">
  	<tr>
      	<td valign="top" align="center" colspan="4"><div class="logo">[[PDFLOGO]]</div></td>
  	</tr>
      
      <tr>   
      	<td valign="top" colspan="2">
      	 <div style="border-bottom: 1px solid #000000;">
          <?php echo apply_filters( 'pdf_template_billing_details_text', __( '<h3>BILLING DETAILS</h3>', 'woocommerce-pdf-invoice' ) ); ?>
         </div>
          <div style="padding: 10px 0;">
  		    [[PDFBILLINGADDRESS]]<br />
          [[PDFBILLINGTEL]]<br />
          [[PDFBILLINGEMAIL]]<br />
          [[PDFBILLINGVATNUMBER]]
          </div>
      	</td>
      	<td valign="top" colspan="2">
        <div style="border-bottom: 1px solid #000000; margin-bottom: 20px;">
      	 <?php echo apply_filters( 'pdf_template_shipping_details_text', __( '<h3>SHIPPING DETAILS</h3>', 'woocommerce-pdf-invoice' ) ); ?>
        </div>
          <div style="padding: 10px 0;">
  		    [[PDFSHIPPINGADDRESS]]
          </div>
      	</td>
      </tr>
    </table>
  </div> 
   
  <div id="content">
    [[ORDERINFO]]
  	<table table width="100%">
      	<tr>
          	<td width="60%" valign="top">
              [[PDFORDERNOTES]]
          	</td>
          	<td width="40%" valign="top" align="right">
            	<table width="100%" class="totals">
                [[PDFORDERTOTALS]]
            	</table>
          	</td>
  		</tr>
  	</table>

    </div> 

  <div id="footer"> 
    <div style="border: 1px solid #222222; margin-bottom: 20px;">
      <div id="fineprint" style="margin: 10px;">
        <div style="font-size:8px; text-align: center; margin: auto;">Returns offered on faulty items returned within seven working days with original packaging, tag and receipt.<br>
        All return requests must be sent via email to info@katvanduinen.com in order to be processed and accepted –<br>
        this should be done before returning item. Cost of return courier and any tax, duties or customs fees incurred for delivery of item only refunded if<br>
        item is accepted as faulty by Kat van Duinen following inspection on return. For full T&Cs, visit www.katvanduinen.com.</div>
      </div>
    </div>
    <div class="copyright"><p style="text-transform: uppercase; font-weight: bold;">www.katvanduinen.com</p></div>
    <div class="copyright"><?php echo apply_filters( 'pdf_template_registered_name_text', __( '', 'woocommerce-pdf-invoice' ) ); ?>[[PDFREGISTEREDNAME]] <?php echo apply_filters( 'pdf_template_company_number_text', __( '', 'woocommerce-pdf-invoice' ) ); ?>[[PDFCOMPANYNUMBER]] </div>
    <div class="copyright"><?php echo apply_filters( 'pdf_template_registered_office_text', __( '', 'woocommerce-pdf-invoice' ) ); ?>[[PDFREGISTEREDADDRESS]]</div>
    <div class="copyright"><?php echo apply_filters( 'pdf_template_vat_number_text', __( 'VAT Number : ', 'woocommerce-pdf-invoice' ) ); ?>[[PDFTAXNUMBER]]</div>
  </div>
</body> 
</html> 