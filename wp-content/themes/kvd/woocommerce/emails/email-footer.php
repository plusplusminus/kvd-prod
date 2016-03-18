<?php
/**
 * Email Footer
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
															</div>
														</td>
                                                    </tr>
                                                </table>
                                                <!-- End Content -->
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Body -->
                                </td>
                            </tr>
                        	<tr>
                            	<td align="center" valign="top">
                                    <!-- Footer -->
                                	<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer">
                                    	<tr>
                                        	<td valign="top">
                                                <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td colspan="2" valign="top" id="signature">
                                                            <p>Regards,</p>
                                                            <?php $url = site_url(); ?>
                                                            <?php if ($url == 'http://katvanduinen.com') { ?>
                                                                <img src="http://katvanduinen.com/wp-content/uploads/2015/12/signature.png" alt="Kat van Duinen">
                                                            <?php } else { ?>

                                                                <img src="http://www.katvanduinen.com/wp-content/uploads/2015/12/signature.png" alt="Kat van Duinen">

                                                            <?php } ?>
                                                            <p>Kat van Duinen<br>
                                                            Founder &amp; President</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" valign="middle" id="credit">
                                                            <p style: font-weight: bold;>&#9472;</p>
                                                        	<?php echo wpautop( wp_kses_post( wptexturize( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ) ) ); ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Footer -->
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
