<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sites extends MY_Controller {

    /**
     * Class constructor
     *
     * Loads required models, check if user has right to access this class, load the hook class and add a hook point
     *
     * @return  void
     */
    public function __construct()
    {
        parent::__construct();

        $model_list = [
            'user/Users_model' => 'MUsers',
            'package/Packages_model' => 'MPackages',
            'settings/Payment_settings_model' => 'MPayments',
            'sites/Blocks_fav_model' => 'MBlocksFav',
            'sites/Frames_model' => 'MFrames',
            'sites/Sites_model' => 'MSites',
            'sites/Pages_model' => 'MPages',
            'shared/Revision_model' => 'MRevisions',
            'shared/Ftp_model' => 'MFtp',
            'account/Domain_model' => 'MDomain'
        ];
        $this->load->model($model_list);

        if ( ! $this->session->has_userdata('user_id') && $this->uri->segment(1) != 'loadsinglepage' && $this->uri->segment(1) != 'loadsingleframe')
        {
            redirect('auth', 'refresh');
        }

        $this->hooks = load_class('Hooks', 'core');
        $this->data = [];

        /** Hook point */
        $this->hooks->call_hook('sites_construct');
    }
    public function bulkDelete()
      {
          foreach ($_POST['info'] as $id)
          {
              $this->MSites->trash($id);
          }
      }

    /**
     * Loads site's dashboard
     *
     * @return  void
     */

     public function my_account()
 {
   /*
     $id = $this->uri->segment(3);
     $p = $id = $this->uri->segment(4);

   if ($id)
     {
       $user = $this->MUsers->get_by_id($id);
       $package = $this->MPackages->get_by_id($p);

       if ($user['package_id'] != $package['id'])
         {
             if ($user['package_price'] == 0 && $package['price'] == 0)
             {
                 $this->MUsers->update_field($user['id'], 'package_id', $package['id']);

                 $return = array();
                 $temp = array();
                 $temp['header'] = $this->lang->line('user_package_update_success_heading');
                 $temp['content'] = $this->lang->line('user_package_update_success_message');
                 $return['responseCode'] = 1;
                 $return['responseHTML'] = $this->load->view('shared/success', array('data'=>$temp), TRUE);
                 redirect('sites/myaccount');
             }
             else
             {

             $session_data =
                 array(
                     'price' => $package['price'],
                     'pack_id' => $package['id'],
                     'user_id' => $this->input->post('user_id')
                 );

             $this->session->set_userdata('pack', $session_data);

           //Initialise the cURL var
           $ret="http://mywebbiy.com/sites/billing";
           $ca="http://mywebbiy.com";

           $ch = curl_init();
           //Get the response from cURL
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           //Set the Url
           curl_setopt($ch, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');
   curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
 'USER' => 'theinkgallery_api1.gmail.com',
 'PWD' => 'TVCKS3GUUX5BDWFX',
 'SIGNATURE' => 'AiPC9BjkCyDFQXbSkoZcgqH3hpacAID1avsqPjlWaVhJf8eJsQxwrUxy',
 'METHOD' => 'SetExpressCheckout',
 'VERSION' => '108',
 'LOCALECODE' => 'en_US',
 'PAYMENTREQUEST_0_AMT' => $package['price'],
 'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD',
 'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
 'PAYMENTREQUEST_0_ITEMAMT' => $package['price'],
 'L_PAYMENTREQUEST_0_NAME0' => 'Webbiy.com',
 'L_PAYMENTREQUEST_0_DESC0' => 'Website Subscription',
 'L_PAYMENTREQUEST_0_QTY0' => 1,
 'L_PAYMENTREQUEST_0_AMT0' => $package['price'],
 'L_BILLINGTYPE0' => 'RecurringPayments',
 'L_BILLINGAGREEMENTDESCRIPTION0' => 'Website Subscription',
 'CANCELURL' => $ca,
 'RETURNURL' => $ret
)));

$response = curl_exec($ch);
//     var_dump($response);
//     exit();
 redirect("https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&".$response);

             }
         }

     }*/
      redirect('account/upgrade');
 }

function movetouser($id) {
    $rtn = array();
    if($id) {
        $site = $this->MSites->get_by_id($id);

        if($site) {
          $user = $this->input->post("user");

          /*
  $pass = $this->input->post("pass");
          $userid = $this->MUsers->createFast($user,$pass); */
          $userid = $this->MUsers->get_by_email($user)['id'];


          if($userid) {
              $this->MSites->changeOwner($userid,$id);
                  echo 'success';
          } else {
            echo 'no user found';
          }








        } else {
          echo 'error';
        }

    } else {
  echo 'error';
    }

}


 function publish_history() {

     if ($this->session->userdata('user_type') == "Admin")
     {
       $this->db->from('publish_log');
      $q = $this->db->get();
      if ($q->num_rows() > 0)
      {
          foreach ($q->result_array() as $row)
          {
              $dbd[] = $row;
          }
      }



    $this->load->view('shared/publish_history',array('data'=>$dbd));

 } else {

   redirect('/');
 }

 }



 function billing(){

     $token=$this->input->get("token");
     $payerid=$this->input->get("PayerID");

     $date=date('Y-m-d');
     $ate = date('Y-m-d', strtotime($date . ' +1 day'));
     $p = 'T16:00:00Z';
     $te = $ate.$p;
     $month="Day";
     $fail="3";
     $f="3";
     $d = "ok";
     $poa = 1;
     $pop="SALE";
     $version=86;

             //Initialise the cURL var
$review = $this->session->userdata['pack'];
$review['price'];
$ch = curl_init();
//Get the response from cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//Set the Url
curl_setopt($ch, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');
curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query(array(
'USER' => 'theinkgallery_api1.gmail.com',
'PWD' => 'TVCKS3GUUX5BDWFX',
'SIGNATURE' => 'AiPC9BjkCyDFQXbSkoZcgqH3hpacAID1avsqPjlWaVhJf8eJsQxwrUxy',
'METHOD' => 'CreateRecurringPaymentsProfile',
'VERSION' => '108',
'LOCALECODE' => 'en_US',
'TOKEN' => $token,
'PayerID' => $payerid,
'PROFILESTARTDATE' => $te,
 'DESC' => 'Website Subscription',
 'BILLINGPERIOD' => 'Month',
 'BILLINGFREQUENCY' => '1',
 'AMT' => $review['price'],
 'CURRENCYCODE' => 'USD',
 'COUNTRYCODE' => 'US',
 'MAXFAILEDPAYMENTS' => 3
))
);

$response = curl_exec($ch);
//var_dump($response);
//exit();
redirect("http://mywebbiy.com/sites/finish?".$response);
 }


 function finish()
 {
    if($_GET['ACK'] == 'Success')
    {
     $BILLINGAGREEMENTID = $_GET['PROFILEID'];
     $TIMESTAMP = $_GET['TIMESTAMP'];
     $ACK = $_GET['ACK'];
     $TRANSACTIONID = $_GET['TRANSACTIONID'];
     $TRANSACTIONTYPE = $_GET['TRANSACTIONTYPE'];
     $PAYMENTTYPE = $_GET['PAYMENTTYPE'];
     $ORDERTIME = $_GET['ORDERTIME'];
     $AMT = $_GET['AMT'];
     $FEEAMT = $_GET['FEEAMT'];
     $TAXAMT = $_GET['TAXAMT'];
     $PAYMENTSTATUS = $_GET['PAYMENTSTATUS'];
     $CURRENCYCODE = $_GET['CURRENCYCODE'];
     $review = $this->session->userdata['pack'];
      $data = array(
           'BILLINGAGREEMENTID' => $BILLINGAGREEMENTID,
           'TIMESTAMP' => $TIMESTAMP,
           'ACK' => $ACK,
           'AMT' => 0,
            'user_id' => $this->session->userdata['user_id']
        //   'AMT' => $review['price'],
        //   'user_id' => $review['user_id']
       );
      // echo '<pre>';
      // var_dump($this->session->userdata['user_id']);
      // exit();

       $ok = $this->db->insert('paypal',$data);
       if($ok)
       {
           $this->MUsers->update_field($this->session->userdata['user_id'], 'package_id', 14);
           $this->session->unset_userdata('pack');

       echo 'Your Payment Successfull';
       echo '<br> <a href="'.base_url().'sites/templates">Click Here</a>';
       //header('Refresh: 10; URL=sites');
       }
       else
       {
         echo 1;
         print_r($_GET);
         //redirect('sites');
       }

    }
    else
    {
    echo 'Payment fail';
         //redirect('store/register');
    }
 }

  public function myaccount()
 {
   redirect('account/upgrade');
   /*
   $data['title'] = $this->lang->line('sites_index_title');
     $data['content'] = 'myaccount';
     $data['page'] = 'myaccount';
     // Grab us some sites
     //$data['sites'] = $this->MSites->all();
     // Get all users
     $data['users'] = $this->MUsers->get_all();
     $data['packages'] = $this->MPackages->get_all();
     $package = $this->MPackages->get_by_id($this->session->userdata('package_id'));
     $sites = $this->MSites->site_by_user($this->session->userdata('user_id'));
     if (count($package) > 0)
     {
         $user_sites = count($sites);
         $package_sites = (int)$package['sites_number'];
         if ($user_sites > 0)
         {
             // User's site is more or equal to its package number
             if ($user_sites >= $package_sites)
             {
               echo  $data['site_limitation'] = $this->lang->line('sites_index_reach_site_number');
             }
             else if ($user_sites + 2 >= $package_sites)
             {
             echo    $data['site_limitation'] = $this->lang->line('sites_index_almost_reach_site_number');
             }
         }
     }




     $this->load->view('layout', $data);
     if ($this->session->userdata('user_type') != "Admin") {
       $data = array(
        'userid' => $this->session->userdata('user_id'),
        'username' =>$this->session->userdata('user_email'),
        'category' => "Upgrade Page",
        'description' => "User visit to [Upgrade] page",
        'ip_address' => $this->input->ip_address());
        $this->db->insert('tblactivitylog',$data);
     }*/
 }

   public function edit($id)
 {
 $temlate_page_id = $id;

     // Check if it is admin or normal user
    /* if ($this->session->userdata('user_type') == "Admin")
     {
         // Create a new, empty site
         $site_id = $this->MSites->createNew();
   redirect('sites/' . $site_id, 'refresh');
     }
     else
     {*/
         // Check if user package support create new site
         $package = $this->MPackages->get_by_id($this->session->userdata('package_id'));
         $sites = $this->MSites->site_by_user($this->session->userdata('user_id'));

         // User has some sites
         if (count($sites) > 0)
         {
             $site_id = $this->MSites->createNewtemplateSite();

             // User's site is more or equal its package number
             /*if (count($sites) >= $package['sites_number'])
             {
                 $this->session->set_flashdata('error', $this->lang->line('sites_create_site_exceed'));
                 redirect('sites', 'refresh');
             }
             else
             {
                 // Create a new, empty site
                 $site_id = $this->MSites->createNewtemplateSite();
             }*/
         }
         else
         {
             // Create a new, empty site
             $site_id = $this->MSites->createNewtemplateSite();
         }
     //}
 $ids = explode('-',$site_id);
 $site_id = $ids[0];
 $page_id= $ids[1];
//echo $page_id;
 $succes = $this->MSites->savePageTemplateToSite($site_id,$page_id,$temlate_page_id);
 //echo("Location: ".'/sites/' . $site_id);
 //header("Location: ".'/sites/' . $site_id);
    redirect('sites/' . $site_id, 'refresh');
 }

public function changetemplatename() {
  if( $this->session->user_type =='Admin') {
    $data = array('pages_name'    => $_POST['name']);

    $this->db->where('pages_id', $_POST['id']);
    $this->db->update('pages', $data);
    echo 'Saved';
  } else {
      echo 'Login to admin account';
  }
}

 public function templates()
 {
   //$this->session->set_userdata(array('all_templates'=>true));
 $pages = $this->MPages->getAllTemplatesFast();
/*
foreach ($pages as $keyx => $valuex) {

$valuez = $valuex[0];

if($valuez['thumb']) {
//echo '1';
} else {
//echo 2;
//print_r($valuez);
  $page_id = $valuez['pageID'];
  $screenshot_url = 'http://mywebbiy.com/loadsinglepage/' . $page_id;
  $filename = 'templatethumb_' . $page_id . '.jpg';
$this->load->library('screenshot_library');
$screenshot = $this->screenshot_library->make_screenshot($screenshot_url, $filename, $this->input->post('frames_width') . 'x' . $this->input->post('frames_height'), $this->config->item('screenshot_sitethumbs_folder'));
//echo $page_id;
if ($screenshot)
{

    $config['source_image'] = $this->config->item('screenshot_blockhumbs_folder') . $screenshot;
    $config['width'] = 520;

    $this->load->library('image_lib', $config);

    $this->image_lib->resize();

    $blockthumb = $this->config->item('screenshot_sitethumbs_folder') . $screenshot;
  //  echo $blockthumb;
    $this->MBlocksFav->update_template($page_id, $blockthumb);



}


 //break;
  }


}*/
//exit;
/*
$frame_id = $this->MFrames->insert_frames_as_fav($this->input->post());
$block_id = $this->MBlocksFav->insert($frame_id, $this->input->post());
$screenshot_url = base_url() . 'loadsingleframe/' . $frame_id;
$filename = 'blockthumb_' . $block_id . '.jpg';

$this->load->library('screenshot_library');
$screenshot = $this->screenshot_library->make_screenshot($screenshot_url, $filename, $this->input->post('frames_width') . 'x' . $this->input->post('frames_height'), $this->config->item('screenshot_blockhumbs_folder'));

if ($screenshot)
{

    // resize the image
    $config['source_image'] = $this->config->item('screenshot_blockhumbs_folder') . $screenshot;
    $config['width'] = 520;

    $this->load->library('image_lib', $config);

    $this->image_lib->resize();

    $blockthumb = $this->config->item('screenshot_blockhumbs_folder') . $screenshot;
    $this->MBlocksFav->update_field($block_id, 'blocks_thumb', $blockthumb);

    $this->data['return']['block'] = $this->MBlocksFav->getsingle($block_id);

    $this->data['return']['responseCode'] = 1;

}

*/




 if ($pages)
 {
   $this->data['templates'] = $this->load->view('shared/templateframesuser', array('pages'=>$pages), TRUE);


 }
 //$this->data['builder'] = TRUE;
 //$data['title']="SBPro Templates";
//     $this->data['page'] = "site";
 $this->data['content'] = "templates";


 //$data['title'] = $this->lang->line('sites_index_title');
     //$data['content'] = 'sites';
     //$data['page'] = 'site';
     // Grab us some sites
    // $this->data['sites'] = $this->MSites->all();
     // Get all users
  //   $this->data['users'] = $this->MUsers->get_all();
    // $this->data['packages'] = $this->MPackages->get_all();

    // $package = $this->MPackages->get_by_id($this->session->userdata('package_id'));
  //   $sites = $this->MSites->site_by_user($this->session->userdata('user_id'));
/*     if (count($package) > 0)
     {
         $user_sites = count($sites);
         $package_sites = (int)$package['sites_number'];
         if ($user_sites > 0)
         {
             // User's site is more or equal to its package number
             if ($user_sites >= $package_sites)
             {
                 $this->data['site_limitation'] = $this->lang->line('sites_index_reach_site_number');
             }
             else if ($user_sites + 2 >= $package_sites)
             {
                 $this->data['site_limitation'] = $this->lang->line('sites_index_almost_reach_site_number');
             }
         }
     }
*/
 //print_r($data['packages']);

 $this->load->view('layout', $this->data);


}






    public function index()
    {
        /** Hook point */
        $this->hooks->call_hook('sites_index_pre');

        $this->data['title'] = $this->lang->line('sites_index_title');
        $this->data['content'] = 'sites';
        $this->data['page'] = 'site';
        /** Grab us some sites */

        if( $this->session->user_type == 'Admin'){

            $this->data['sites'] = $this->MSites->all();
            $data = array(
             'userid' => $this->session->userdata('user_id'),
             'username' =>$this->session->userdata('user_email'),
             'category' => "Sites Page",
             'description' => "User visit to [Sites] page",
             'ip_address' => $this->input->ip_address());
             $this->db->insert('tblactivitylog',$data);
          } else {
            $this->data['sites'] = $this->MSites->all($this->session->userdata('user_id'));
          }

        /** Get all users */
      //  $this->data['users'] = $this->MUsers->get_all();



        $gateway = $this->MPayments->get_by_name('payment_gateway');
        $this->data['packages'] = $this->MPackages->get_all($gateway[0]->value);
        $package = $this->MPackages->get_by_id($this->session->userdata('package_id'));
        $sites = $this->MSites->site_by_user($this->session->userdata('user_id'));
        if (count($package) > 0)
        {
            $user_sites = count($sites);
            $package_sites = (int)$package['sites_number'];
            if ($user_sites > 0)
            {
                /** User's site is more or equal to its package number */
                if ($user_sites >= $package_sites)
                {
                    $this->data['site_limitation'] = $this->lang->line('sites_index_reach_site_number');
                }
                else if ($user_sites + 2 >= $package_sites)
                {
                    $this->data['site_limitation'] = $this->lang->line('sites_index_almost_reach_site_number');
                }
            }
        }
        $this->data['usersx'] = $this->MUsers->get_all123();
        /** Hook point */
        $this->hooks->call_hook('sites_index_post');

        $this->load->view('layout', $this->data);
    }

    public function user_activities(){
        if( $this->session->user_type !='Admin'){
            redirect('sites');
        }
           $this->load->library('pagination');

              $config['base_url'] = base_url('sites/user_activities');
          $config['total_rows'] = $this->db->count_all('tblactivitylog');
          $config['per_page'] = "50";
          $config["uri_segment"] = 3;
          $choice = $config["total_rows"] / $config["per_page"];
          $config["num_links"] = floor($choice);

      $config['full_tag_open'] = '<div class="pagination"><ul>';
    $config['full_tag_close'] = '</ul></div>';

    $config['first_link'] = '« First';
    $config['first_tag_open'] = '<li class="prev page">';
    $config['first_tag_close'] = '</li>';

    $config['last_link'] = 'Last »';
    $config['last_tag_open'] = '<li class="next page">';
    $config['last_tag_close'] = '</li>';

    $config['next_link'] = 'Next →';
    $config['next_tag_open'] = '<li class="next page">';
    $config['next_tag_close'] = '</li>';

    $config['prev_link'] = '← Previous';
    $config['prev_tag_open'] = '<li class="prev page">';
    $config['prev_tag_close'] = '</li>';

    $config['cur_tag_open'] = '<li class="active"><a href="">';
    $config['cur_tag_close'] = '</a></li>';

    $config['num_tag_open'] = '<li class="page">';
    $config['num_tag_close'] = '</li>';


          $this->pagination->initialize($config);
          $data['page'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

          //call the model function to get the department data
          $data['activityLog'] = $this->MSites->getUserActivityLog($config["per_page"], $data['page']);


          $data['title'] = 'User Activities';
          $data['content'] = 'user_activities';
          $data['page'] = 'user_activities';

          $this->load->view('layout', $data);
      }
    /**
     * Loads page builder
     *
     * @return  void
     */
    public function create()
    {
        /** Hook point */
        $this->hooks->call_hook('sites_create_pre');

        /** Check if it is admin or normal user */
        if ($this->session->userdata('user_type') == "Admin")
        {
            /** Create a new, empty site */
            $site_id = $this->MSites->createNew();
        }
        else
        {
          $data = array(
          'userid' => $this->session->userdata('user_id'),
          'username' =>$this->session->userdata('user_email'),
          'category' => "Create New Site",
          'description' => "User visit to [Create New Site] page",
          'ip_address' => $this->input->ip_address());
          $this->db->insert('tblactivitylog',$data);
            /** Check if user package support create new site */
            $package = $this->MPackages->get_by_id($this->session->userdata('package_id'));
            $sites = $this->MSites->site_by_user($this->session->userdata('user_id'));

            /** User has some sites */
            if (count($sites) > 0)
            {
                /** User's site is more or equal its package number */
                if (count($sites) >= $package['sites_number'])
                {
                    $this->session->set_flashdata('error', $this->lang->line('sites_create_site_exceed'));
                    redirect('sites', 'refresh');
                }
                else
                {
                    /** Create a new, empty site */
                    $site_id = $this->MSites->createNew();
                }
            }
            else
            {
                /** Create a new, empty site */
                $site_id = $this->MSites->createNew();
            }
        }
        redirect('/sites/'.$site_id, 'refresh');


    }

    /**
     * Saves page as a template for future use
     *
     * @return  json        $return
     */
    public function tsave()
    {
        /** Hook point */
        $this->hooks->call_hook('sites_tsave_pre');

        /** Do we have some frames to save? */
        reset($_POST['pages']);
        $first_key = key($_POST['pages']);

        if ( !isset($_POST['pages'][$first_key]['blocks']) ) {

            $temp = array();
            $temp['header'] = $this->lang->line('sites_tsave_no_page_error_heading');
            $temp['content'] = $this->lang->line('sites_tsave_no_page_error_message');

            $this->return = array();
            $this->return['responseCode'] = 0;
            $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), TRUE);

            die(json_encode($this->return));

        }

        $templateID = $this->MPages->saveTemplate($_POST['pages'], $_POST['fullPage'], $_POST['templateID']);

        // $this->return = array();

        /** All good */
        if ($templateID)
        {
            $temp = array();
            $temp['header'] = $this->lang->line('sites_tsave_template_save_success_heading');
            $temp['content'] = $this->lang->line('sites_tsave_template_save_success_message');

            $this->return = array();
            $this->return['responseCode'] = 1;
            $this->return['templateID'] = $templateID;
            $this->return['responseHTML'] = $this->load->view('shared/success', array('data'=>$temp), TRUE);
        }
        /** Not good */
        else
        {
            $temp = array();
            $temp['header'] = $this->lang->line('sites_tsave_template_save_fail_heading');
            $temp['content'] = $this->lang->line('sites_tsave_template_save_fail_message');

            $this->return = array();
            $this->return['responseCode'] = 0;
            $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), TRUE);
        }

        /** Hook point */
        $this->hooks->call_hook('sites_tsave_post');

        die(json_encode($this->return));
    }

    /**
     * Used to create new sites AND save existing ones
     *
     * @param   integer     $forPublish
     * @return  json        $return
     */
    public function save($forPublish = 0)
    {
        /** Hook point */
        $this->hooks->call_hook('sites_save_pre');

        /** Do we have the required data? */
        if ( ! isset($_POST['siteData']))
        {
            $temp = array();
            $temp['header'] = $this->lang->line('sites_save_no_data_error_heading');
            $temp['content'] = $this->lang->line('sites_save_no_data_error_message');

            $this->return = array();
            $this->return['responseCode'] = 0;
            $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), TRUE);

            die(json_encode($this->return));
        }

        /** Do we have some frames to save? */
        if (( ! isset($_POST['pages']) || $_POST['pages'] == '') && ( ! isset($_POST['toDelete'])))
        {
            $temp = array();
            $temp['header'] = $this->lang->line('sites_save_no_frame_error_heading');
            $temp['content'] = $this->lang->line('sites_save_no_frame_error_message');

            $this->return = array();
            $this->return['responseCode'] = 0;
            $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), TRUE);

            die(json_encode($this->return));
        }

        /** Should we save an existing site or create a new one? */
        $this->MSites->update($_POST['siteData'], $_POST['pages']);

        /** Delete any pages? */
        if (isset($_POST['toDelete']) && is_array($_POST['toDelete']) && count($_POST['toDelete']) > 0)
        {
            foreach ($_POST['toDelete'] as $page)
            {
                $this->MPages->delete($_POST['siteData']['sites_id'], $page);
            }
        }

        $this->return = array();

        /** Regular site save */
        if ($forPublish == 0)
        {
            $temp = array();
            $temp['header'] = $this->lang->line('sites_save_after_publish_success_heading');
            $temp['content'] = $this->lang->line('sites_save_after_publish_success_message');
        }
        /** Saving before publishing, requires different message */
        else if ($forPublish == 1)
        {
            $temp = array();
            $temp['header'] = $this->lang->line('sites_save_before_publish_success_heading');
            $temp['content'] = $this->lang->line('sites_save_before_publish_success_message');
        }

        $this->return['responseCode'] = 1;
        $this->return['responseHTML'] = $this->load->view('shared/success', array('data'=>$temp), TRUE);

        /** Hook point */
        $this->hooks->call_hook('sites_save_post');

        die(json_encode($this->return));
    }

    /**
     * Loads some configuration data with ajax call
     *
     * @return  json        $return
     */
    public function siteData()
    {
        /** Hook point */
        $this->hooks->call_hook('sites_siteData_pre');

        $this->return = $this->MSites->getSite($this->session->userdata('siteID'));

        /** Delete unneeded stuff */
        unset($this->return['assetFolders']);

        /** Admin or no? */
        if ($this->session->userdata('user_type') == "Admin")
        {
            $this->return['is_admin'] = 1;
        }
        else
        {
            $this->return['is_admin'] = 0;
        }

        if ($this->config->item('google_api') !== '')
        {
            $this->return['google_api'] = $this->config->item('google_api');
        }

        /** Hook point */
        $this->hooks->call_hook('sites_siteData_post');

        echo json_encode($this->return);
    }

    /**
     * Get and retrieve single site data
     *
     * @param   integer     $siteID
     * @return  void
     */
    public function site($siteID)
    {
        /** Hook point */
        $this->hooks->call_hook('sites_site_pre');

        $this->load->helper('thumb');

        /** Store the session ID with this session */
        $this->session->set_userdata('siteID', $siteID);

        /** If user is not an admin, we'll need to check of this site belongs to this user */
        if ($this->session->userdata('user_type') != "Admin")
        {
            if ( ! $this->MSites->isMine($siteID))
            {
                redirect('/sites');
            }

            // $hosting = $this->MPackages->get_by_id($this->session->userdata('package_id'));
            // $this->data['hosting_option'] = json_decode($hosting['hosting_option']);
            // print_r($this->data['hosting_option']);
            // die();
        }

        $siteData = $this->MSites->getSite($siteID);
        if ($siteData == FALSE)
        {
            /** Site could not be loaded, redirect to /sites, with error message */
            $this->session->set_flashdata('error', $this->lang->line('sites_site_could_not_load_error'));
            redirect('/sites', 'refresh');
        }
        else
        {
            $this->data['siteData'] = $siteData;

            /** Get page data */
            $pagesData = $this->MPages->getPageData($siteID);
            if ($pagesData)
            {
                $this->data['pagesData'] = $pagesData;
            }

            /** Collect data for the image library */
            $userID = $this->session->userdata('user_id');;
            $userImages = $this->MUsers->getUserImages($userID);
            if ($userImages)
            {
                $this->data['userImages'] = $userImages;
            }
            else
            {
                $this->data['userImages'] = [];
            }

            $adminImages = $this->MSites->adminImages();
            if ($adminImages)
            {
                $this->data['adminImages'] = $adminImages;
            }
            else
            {
                $this->data['adminImages'] = [];
            }

            $siteImages = $this->MSites->siteImages($siteID);
            if ($siteImages)
            {
                $this->data['siteImages'] = $siteImages;
            }
            else
            {
                $this->data['siteImages'] = [];
            }

            /** Pre-build templates */
            if ($this->session->userdata('user_type') == 'Admin')
            {
                $pages = $this->MPages->getAllTemplates();
            }
            else
            {
                $package = $this->MPackages->get_by_id($this->session->userdata('package_id'));
                if (json_decode($package['templates']) == NULL)
                {
                    $pages = NULL;
                }
                else
                {
                    $pages = $this->MPages->getAllTemplates(json_decode($package['templates'], TRUE));
                }
            }

            if ($pages)
            {
                $this->data['templates'] = $this->load->view('shared/templateframes', array('pages'=>$pages), TRUE);
            }

            /** Grab all revisions */
            $this->data['revisions'] = $this->MRevisions->getForSite($siteID, 'index');
            /** Grab pacakge details */
            $this->data['package'] = $this->MPackages->get_by_id($this->session->userdata('package_id'));
            //print_r($this->data['package']); die();

            $this->data['builder'] = TRUE;
            $this->data['page'] = "site_builder";
            $this->data['content'] = "create";

            /** Hook point */
            $this->hooks->call_hook('sites_site_post');

            $this->load->view('layout', $this->data);
        }
    }

    /**
     * Get and retrieve single site data with ajax
     *
     * @param   string      $siteID
     * @return  json        $return
     */
    public function siteAjax($siteID = '')
    {
        /** Hook point */
        $this->hooks->call_hook('sites_siteAjax_pre');

        /** If siteID is missing */
        if ($siteID == '' || $siteID == 'undefined')
        {
            $temp = array();
            $temp['header'] = $this->lang->line('sites_siteAjax_siteID_missing_error_heading');
            $temp['content'] = $this->lang->line('sites_siteAjax_siteID_missing_error_message');

            $this->return = array();
            $this->return['responseCode'] = 0;
            $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), TRUE);

            die(json_encode($this->return));
        }

        $siteData = $this->MSites->getSite($siteID);
        /** Remove unnecessary data */
        unset($siteData['pages']);
        unset($siteData['assetFolders']);
        /** Check if ssh2 module is loaded */
        if (extension_loaded('ssh2'))
        {
            $siteData['ssh2'] = TRUE;
        }

        /** All did not go well */
        if ($siteData == FALSE)
        {
            $temp = array();
            $temp['header'] = $this->lang->line('sites_siteAjax_site_save_error_heading');
            $temp['content'] = $this->lang->line('sites_siteAjax_site_save_error_message');

            $this->return = array();
            $this->return['responseCode'] = 0;
            $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), TRUE);

            /** Hook point */
            $this->hooks->call_hook('sites_siteAjax_error');

            echo json_encode($this->return);
        }
        /** All went well */
        else
        {
            $this->return = array();
            $this->return['responseCode'] = 1;
            $this->return['responseHTML'] = $this->load->view('shared/sitedata', array('data' => $siteData), TRUE);

            /** Hook point */
            $this->hooks->call_hook('sites_siteAjax_success');

            echo json_encode($this->return);
        }
    }

    /**
     * Updates site details, submitting through ajax
     *
     * @return  json        $return
     */
    public function siteAjaxUpdate()
    {
        /** Hook point */
        $this->hooks->call_hook('sites_siteAjaxUpdate_pre');

        $this->form_validation->set_rules('siteID', 'Site ID', 'required');
        //$this->form_validation->set_rules('sites_name', 'Site name', 'required');
        /** All did not go well */
        if ($this->form_validation->run() == FALSE)
        {
            $temp = array();
            $temp['header'] = $this->lang->line('sites_siteAjaxUpdate_validation_error_heading');
            $temp['content'] = $this->lang->line('sites_siteAjaxUpdate_validation_error_message') . validation_errors();

            $this->return = array();
            $this->return['responseCode'] = 0;
            $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), TRUE);

            echo json_encode($this->return);
        }
        /** All good with the data, let's update */
        else
        {
            /** check if sub folder already exist */
            if (trim($this->input->post('sub_folder')) != "")
            {
                $sub_folder = $this->MSites->get_by_field_value('sub_folder', trim($this->input->post('sub_folder')));
                if (count($sub_folder) > 0)
                {
                    $arr = array_filter($sub_folder, function($ar)
                    {
                        return ($ar['sites_id'] != $this->input->post('siteID'));
                    });
                    if (count($arr) > 0)
                    {
                        $temp = array();
                        $temp['header'] = $this->lang->line('sites_siteAjaxUpdate_sub_folder_error_heading');
                        $temp['content'] = $this->lang->line('sites_siteAjaxUpdate_sub_folder_error_message') . validation_errors();

                        $this->return = array();
                        $this->return['responseCode'] = 0;
                        $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), TRUE);

                        echo json_encode($this->return);
                        die();
                    }
                }
            }

            /** check if sub domain already exist */
            if (trim($this->input->post('sub_domain')) != "")
            {
                $sub_domain = $this->MSites->get_by_field_value('sub_domain', trim($this->input->post('sub_domain')));
                if (count($sub_domain) > 0)
                {
                    $arr = array_filter($sub_domain, function($ar)
                    {
                        return ($ar['sites_id'] != $this->input->post('siteID'));
                    });
                    if (count($arr) > 0)
                    {
                        $temp = array();
                        $temp['header'] = $this->lang->line('sites_siteAjaxUpdate_sub_domain_error_heading');
                        $temp['content'] = $this->lang->line('sites_siteAjaxUpdate_sub_domain_error_message') . validation_errors();

                        $this->return = array();
                        $this->return['responseCode'] = 0;
                        $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), TRUE);

                        echo json_encode($this->return);
                        die();
                    }
                }
            }

            /** check if custom domain already exist */
            if (trim($this->input->post('custom_domain')) != "")
            {
                $custom_domain = $this->MSites->get_by_field_value('custom_domain', trim($this->input->post('custom_domain')));
                if (count($custom_domain) > 0)
                {
                    $arr = array_filter($custom_domain, function($ar)
                    {
                        return ($ar['sites_id'] != $this->input->post('siteID'));
                    });
                    if (count($arr) > 0)
                    {
                        $temp = array();
                        $temp['header'] = $this->lang->line('sites_siteAjaxUpdate_custom_domain_error_heading');
                        $temp['content'] = $this->lang->line('sites_siteAjaxUpdate_custom_domain_error_message') . validation_errors();

                        $this->return = array();
                        $this->return['responseCode'] = 0;
                        $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), TRUE);
                        echo json_encode($this->return);
                        die();
                    }
                }
            }

            /** If this site is home page then remove all home page flag first */
            if ($this->input->post('home_page') == 1)
            {
                $this->MSites->remove_home_page();
            }

            $update = $this->MSites->updateSiteData($this->input->post());
            if ($update['return'])
            {
                $temp = array();
                $temp['header'] = $this->lang->line('sites_siteAjaxUpdate_save_success_heading');
                if ($update['ftp_ok'] == 1)
                {
                    $temp['content'] = $this->lang->line('sites_siteAjaxUpdate_save_ftp_success_message');
                }
                else
                {
                    $temp['content'] = $this->lang->line('sites_siteAjaxUpdate_save_ftp_error_message');
                }

                $this->return = array();
                $this->return['responseCode'] = 1;
                $this->return['responseHTML'] = $this->load->view('shared/success', array('data'=>$temp), TRUE);

                if ($update['ftp_ok'] == 1)
                {
                    $this->return['ftpOK'] = 1;
                }
                else
                {
                    $this->return['ftpOK'] = 0;
                }

                /** We'll send back the updated site data as well */
                $siteData = $this->MSites->getSite($this->input->post('siteID'));
                $this->return['responseHTML2'] = $this->load->view('shared/sitedata', array('data'=>$siteData), TRUE);
                $this->return['siteName'] = $siteData['site']->sites_name;
                $this->return['siteID'] = $siteData['site']->sites_id;
                $this->return['siteSubFolder'] = $siteData['site']->sub_folder;

                if($siteData['site']->custom_domain) {

                    $import = $this->MDomain->import_domain($siteData['site']->custom_domain);
                 
                    $datadb = array(
                          'user' => $this->session->userdata['user_id'],
                          'site' => $siteData['site']->sites_id,
                          'type' => 'domain',
                          'time' => time(),
                          'domain' => $siteData['site']->custom_domain,
                          'data' => json_encode($import)
                     );
                     $ok = $this->db->insert('publish_log',$datadb);


                } else {
                  $datadb = array(
                        'user' => $this->session->userdata['user_id'],
                        'site' => $siteData['site']->sites_id,
                        'type' => 'subdomain',
                        'time' => time(),
                        'domain' => $this->input->post('sub_domain')
                   );
                   $ok = $this->db->insert('publish_log',$datadb);
                }



                if($import['error']) {
                  $this->return['responseHTML'] = '<script>alert("Invalid domain");</script>';  echo json_encode($this->return);exit;
                } else {
                  $siteData['ns']['ns_table'] = $import['ns_table'];
                  $siteData['ns']['ns_header'] =  $import['ns_header'];

                  $maildata['domain'] = $siteData['site']->custom_domain;
                 /* $maildata['ns01'] = $import['ns']->original_name_servers[0];
                  $maildata['ns02'] = $import['ns']->original_name_servers[1];
                  $maildata['ns11'] = $import['ns']->name_servers[0];
                  $maildata['ns12'] = $import['ns']->name_servers[1];*/
                   $maildata['ns01'] = $import['ns']['name_0'];
                  $maildata['ns02'] = $import['ns']['name_0'];
                  $maildata['ns11'] = $import['ns']['name_0'];
                  $maildata['ns12'] = $import['ns']['name_0'];

                  $this->email->from($this->config->item('sent_email_from_address'), $this->config->item('sent_email_from_name'));
                  $this->email->to($this->session->userdata('user_email'));
                  $this->email->subject('Change NS records for '.$siteData['site']->custom_domain.' (pending)');
                  $this->email->message($this->load->view('sent/email_domain', array('data'=>$maildata), TRUE));
                  $this->email->send();



                  /** Hook point */
                  $this->hooks->call_hook('sites_siteAjaxUpdate_success');
                  //echo 12312313231;exit;
                  $this->return['responseHTML'] = '';
                  $this->return['responseHTML2'] = $this->load->view('shared/sitedata2', array('data'=>$siteData), TRUE);

                  header("HTTP/1.1 200 OK",true,200);
                  $this->output->set_header('HTTP/1.1 200 OK');
                  header('X-PHP-Response-Code: 200', true, 200);
                  echo json_encode($this->return);

                //  exit;
                }






            }
            else
            {
                $temp = array();
                $temp['header'] = $this->lang->line('sites_siteAjaxUpdate_save_error_heading');
                $temp['content'] = $this->lang->line('sites_siteAjaxUpdate_save_error_message');

                $this->return = array();
                $this->return['responseCode'] = 0;
                $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), TRUE);

                /** Hook point */
                $this->hooks->call_hook('sites_siteAjaxUpdate_error');

                echo json_encode($this->return);
            }
        }
    }

    /**
     * Gets the content of a saved frame and sends it back to the browser
     *
     * @param   integer     $frameID
     * @return  void
     */
    public function getframe($frameID)
    {
        $frame = $this->MSites->getSingleFrame($frameID);
        $frame->frames_content = str_replace('/css/blocks.css','/css/blocks.css?'.time(),$frame->frames_content);
        echo $frame->frames_content;
    }

    /**
     * Publishes a site via FTP/sFTP with new export method
     *
     * @return  void
     */
    public function publish($site_id)
    {

        /** Hook point */
        $this->hooks->call_hook('sites_publish_pre');

        /** Prevent timeout */
        set_time_limit(0);

        $cssUrls = [];
        $jsUrls = [];
        $pageImages = [];
        $fonts = [];

        $site_data = $this->MSites->siteData($site_id);
        foreach ($this->input->post('pages') as $page)
        {
            /** Get page meta */
            $pageMeta = $this->MPages->getSinglePage($site_id, $page);
            /** Get page content */
            $pageContent = $this->MPages->load_page($pageMeta->pages_id);

            /** fix up bits in the <head> */
            $pageContent = str_replace("<html><head>", "<html>\n<head>", $pageContent);
            $pageContent = str_replace("</head>", "\n</head>", $pageContent);
            $pageContent = str_replace("<style", "\n\t<style", $pageContent);
            $pageContent = str_replace("</style><link", "</style>\n\t<link", $pageContent);

            if ($pageMeta)
            {
                /** Insert title, meta keywords and meta description */
                $meta = "<title>" . $pageMeta->pages_title . '</title>' . "\r\n";
                $meta .= "\t" . '<meta name="description" content="' . $pageMeta->pages_meta_description . '">' . "\r\n";
                $meta .= "\t" . '<meta name="keywords" content="' . $pageMeta->pages_meta_keywords . '">';
                $pageContent = str_replace('<!--pageMeta-->', $meta, $pageContent);

                /** Insert header includes; */
                $includesPlusCss = '';
                if ($pageMeta->pages_header_includes != '')
                {
                    $includesPlusCss .= $pageMeta->pages_header_includes;
                }
                if ($pageMeta->pages_css != '')
                {
                    $includesPlusCss .= "\n\t<style>" . $pageMeta->pages_css . "</style>\n";
                }
                if ($site_data->global_css != '')
                {
                    $includesPlusCss .= "\n\t<style>" . $site_data->global_css . "</style>\n";
                }
                /** Insert header includes */
                $pageContent = str_replace('<!--headerIncludes-->', $includesPlusCss, $pageContent);
            }

            /** Remove frameCovers */
            $pageContent = str_replace('<div class="frameCover" data-type="video"></div>', "", $pageContent);

            /** This is needed for correct exports */
            $pageContent = str_replace('src="../', 'src="', $pageContent);
            $pageContent = str_replace("src='../", "src='", $pageContent);
            $pageContent = str_replace('url(../', 'url(', $pageContent);
            $pageContent = str_replace("url('../", "url('", $pageContent);
            $pageContent = str_replace('url("../', 'url("', $pageContent);
            $pageContent = str_replace('&quot;../bundles/', '&quot;bundles/', $pageContent); //FF needs this

            if ($this->config->item('google_api') !== '')
            {
                $pageContent = str_replace('</body>', '<script async defer src="https://maps.googleapis.com/maps/api/js?key=' . $this->config->item('google_api') . '&callback=initMap"></script>' . '</body>' . "\n", $pageContent);
            }

            /** setup of htmLawed helper */
            $this->load->helper('htmlawed');
            $config = array(
                'tidy' => '1t1n',
                'direct_nest_list' => 1,
                'keep_bad' => 0,
                'css_expression' => 1,
                'elements' => '*',
                'lc_std_val' => 0,
                'make_tag_strict' => 0,
                'no_deprecated_attr' => 0,
                'style_pass' => 1,
                'unique_ids' => 0,
                'parent' => 'body',
                'hook_tag' => 'my_tag_function'
            );

            /** simle html dom library */
            $this->load->library('Simple_html_dom');
            $raw = str_get_html($pageContent, true, true, DEFAULT_TARGET_CHARSET, false);

            /** body first */
            $body = $raw->find('body')[0];
            $sanitzedBody = htmLawed($body, $config);
            $raw->find('body')[0]->innertext = $sanitzedBody;

            /** extract CSS urls */
            foreach ($raw->find('link[rel="stylesheet"]') as $cssLink)
            {
                /** extract CSS link, no need for blob urls */
                if (substr($cssLink->href, 0, 4) != 'blob')
                {
                    $cssUrls[] = $cssLink->href;

                    /** extract images from CSS */
                    if (substr($cssLink->href, 0, 4) != 'http' && substr($cssLink->href, 0, 2) != '//')
                    {
                        $CSS = file_get_contents("./" . $this->config->item('elements_dir') . "/" . $cssLink->href);

                        $re = '/url\(\s*[\'"]?(\S*\.(?:jpe?g|gif|png))[\'"]?\s*\)[^;}]*?/i';
                        if (preg_match_all($re, $CSS, $matches))
                        {
                            foreach ($matches[1] as $img)
                            {
                                $pageImages[] = $img;
                            }
                        }
                    }

                    /** extract fonts from CSS */
                    $re = '/url\(([\'"]?.[^\'"]*\.(eot|woff|woff2|ttf)[\'"]?)\)/i';
                    if (preg_match_all($re, $CSS, $matches))
                    {
                        foreach ($matches[1] as $font)
                        {
                            $fonts[] = $font;
                        }
                    }
                }
            }

            /** extract JS urls */
            foreach ($raw->find('script[src]') as $jsLink)
            {
                $jsUrls[] = $jsLink->src;
            }

            /** extract <image> src */
            foreach ($body->find('img') as $image)
            {
                $pageImages[] = $image->src;
            }

            /** extract CSS background images */
            $re = '/url\(\s*[\'"]?(\S*\.(?:jpe?g|gif|png))[\'"]?\s*\)[^;}]*?/i';
            if (preg_match_all($re, str_replace("&quot;", "'", $raw), $matches))
            {
                foreach ($matches[1] as $img)
                {
                    $pageImages[] = $img;
                }
            }

            /** last minute clean up of <script> tags */
            $raw = str_replace("\n\t\t \n\t</script>", "</script>", $raw);
            $raw = str_replace("\t</body>", "</body>", $raw);

            $final_html = stripslashes($raw);
            if ( ! is_dir(FCPATH . 'tmp/ftp/' . $site_id))
            {
                mkdir(FCPATH . 'tmp/ftp/' . $site_id, 0777, TRUE);
            }
            $handle = fopen(FCPATH . 'tmp/ftp/' . $site_id . '/' . $page . '.html', 'w');
            fwrite($handle, $final_html);
        }

        /** add items in the $pageImages to copy in tmp folder */
        $pageImages = array_unique($pageImages);
        if (count($pageImages) > 0)
        {
            foreach ($pageImages as $image)
            {
                $temp = explode('?', $image);
                $image = $temp[0];
                if (substr($image, 0, mb_strlen($this->config->item('images_uploadDir'))) != $this->config->item('images_uploadDir'))
                {
                    $image_ = $this->config->item('elements_dir') . '/' . $image;
                }
                else
                {
                    $image_ = $image;
                }
                $real_file_info = new SplFileInfo($image_);
                $script_file_info = new SplFileInfo($image);
                /** Add current file to archive */
                if (file_exists($real_file_info->getRealPath()))
                {
                    if ( ! is_dir(FCPATH . 'tmp/ftp/' . $site_id . '/' . $script_file_info->getPath()))
                    {
                        mkdir(FCPATH . 'tmp/ftp/' . $site_id . '/' . $script_file_info->getPath(), 0777, TRUE);
                    }
                    copy($real_file_info->getRealPath(), FCPATH . 'tmp/ftp/' . $site_id . '/' . $script_file_info->getPathname());
                }
            }
        }

        /** add items in the $cssUrls to copy in tmp folder */
        $cssUrls = array_unique($cssUrls);
        if (count($cssUrls) > 0)
        {
            foreach ($cssUrls as $cssUrl)
            {
                $temp = explode('?', $cssUrl);
                $cssUrl = $temp[0];
                $cssUrl_ = $this->config->item('elements_dir') . '/' . $cssUrl;
                $real_file_info = new SplFileInfo($cssUrl_);
                $script_file_info = new SplFileInfo($cssUrl);
                /** Add current file to archive */
                if (file_exists($real_file_info->getRealPath()))
                {
                    if ( ! is_dir(FCPATH . 'tmp/ftp/' . $site_id . '/' . $script_file_info->getPath()))
                    {
                        mkdir(FCPATH . 'tmp/ftp/' . $site_id . '/' . $script_file_info->getPath(), 0777, TRUE);
                    }
                    copy($real_file_info->getRealPath(), FCPATH . 'tmp/ftp/' . $site_id . '/' . $script_file_info->getPathname());
                }
            }
        }

        /** add items in the $jsUrls to copy in tmp folder */
        $jsUrls = array_unique($jsUrls);
        if (count($jsUrls) > 0)
        {
            foreach ($jsUrls as $jsUrl)
            {
                $temp = explode('?', $jsUrl);
                $jsUrl = $temp[0];
                $jsUrl_ = './' . $this->config->item('elements_dir') . '/' . $jsUrl;
                $real_file_info = new SplFileInfo($jsUrl_);
                $script_file_info = new SplFileInfo($jsUrl);
                /** copy current file to tmp folder */
                if (file_exists($real_file_info->getRealPath()))
                {
                    if ( ! is_dir(FCPATH . 'tmp/ftp/' . $site_id . '/' . $script_file_info->getPath()))
                    {
                        mkdir(FCPATH . 'tmp/ftp/' . $site_id . '/' . $script_file_info->getPath(), 0777, TRUE);
                    }
                    copy($real_file_info->getRealPath(), FCPATH . 'tmp/ftp/' . $site_id . '/' . $script_file_info->getPathname());
                }
            }
        }

        /** Add folder structure */
        /** Prep path to assets array */
        /** Only web fonts are still added the old way */
        $temp = explode("|", $this->config->item('export_pathToAssets'));
        foreach ($temp as $thePath)
        {
            /** Create recursive directory iterator */
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($thePath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            foreach ($files as $name => $file)
            {
                if ($file->getFilename() != '.' && $file->getFilename() != '..' && (strpos($file->getFilename(), ".woff") || strpos($file->getFilename(), ".woff2") || strpos($file->getFilename(), ".ttf") || strpos($file->getFilename(), ".eot")))
                {
                    if (strpos($file, 'images/') === FALSE)
                    {
                        $temp = explode("/", $name);
                        array_shift($temp);
                        $newName = implode("/", $temp);
                    }
                    else
                    {
                        $newName = $name;
                    }
                    $file_info = new SplFileInfo($newName);

                    /** copy current file to tmp folder */
                    if (file_exists($file->getRealPath()))
                    {
                        if ( ! is_dir(FCPATH . 'tmp/ftp/' . $site_id . '/' . $file_info->getPath()))
                        {
                            mkdir(FCPATH . 'tmp/ftp/' . $site_id . '/' . $file_info->getPath(), 0777, TRUE);
                        }
                        copy($file->getRealPath(), FCPATH . 'tmp/ftp/' . $site_id . '/' . $file_info->getPathname());
                    }
                }
            }
        }

        function my_tag_function($element, $attribute_array=0)
        {
            if (is_numeric($attribute_array))
            {
                if ($element == 'script')
                {
                    return "</$element>";
                }
                else
                {
                    return "</$element>";
                }
            }

            $string = '';
            foreach ($attribute_array as $k=>$v)
            {
                $string .= " {$k}=\"{$v}\"";
            }

            if ($element == 'script')
            {
                return "<{$element}{$string}> ";
            }
            else
            {
                return "<{$element}{$string}>";
            }
        }

        /** Upload files to FTP location */
        $config['hostname'] = $site_data->ftp_server;
        $config['username'] = $site_data->ftp_user;
        $config['password'] = $site_data->ftp_password;
        $config['path'] = $site_data->ftp_path;
        $config['type'] = $site_data->ftp_type;
        $config['port'] = $site_data->ftp_port;
        if ($config['type'] == 'ftp')
        {
            $this->load->library('ftp');
            if ($this->ftp->connect($config))
            {
                if ($this->ftp->mirror(FCPATH . 'tmp/ftp/' . $site_id . '/', $config['path'] . '/'))
                {
                    /** All went well */
                    $this->MSites->published($site_id);

                    $this->load->helper('file');
                    delete_files(FCPATH . 'tmp/ftp/' . $site_id, true , false, 1);

                    $this->return = array();
                    $this->return['responseCode'] = 1;

                    die(json_encode($this->return));
                }
            }
        }
        else
        {
            $this->load->library('sftp');
            if ($this->sftp->connect($config))
            {
                if ($this->sftp->mirror(FCPATH . 'tmp/ftp/' . $site_id . '/', $config['path'] . '/'))
                {
                    /** All went well */
                    $this->MSites->published($site_id);

                    $this->load->helper('file');
                    delete_files(FCPATH . 'tmp/ftp/' . $site_id, true , false, 1);

                    $this->return = array();
                    $this->return['responseCode'] = 1;

                    die(json_encode($this->return));
                }
            }
        }

        /** Hook point */
        $this->hooks->call_hook('sites_publish_post');

        exit;
    }

    /**
     * Exports a site
     *
     * @return  void
     */
    public function export()
    {
        /** Hook point */
        $this->hooks->call_hook('sites_export_pre');

        $userID = $this->session->userdata('user_id');

        $zip = new ZipArchive();
        $zip->open("./tmp/" . $this->config->item('export_fileName'), ZipArchive::CREATE);

        $cssUrls = [];
        $jsUrls = [];
        $pageImages = [];
        $fonts = [];

        $theSite = $this->MSites->siteData($_POST['siteID']);
        foreach ($_POST['pages'] as $page=>$content)
        {
            /** Get page meta */
            $pageMeta = $this->MPages->getSinglePage($_POST['siteID'], $page);

            /** fix up bits in the <head> */
            $pageContent = str_replace('<html><head>', "<html>\n<head>", $content);
            $pageContent = str_replace('</head>', "\n</head>", $pageContent);
            $pageContent = str_replace('<style', "\n\t<style", $pageContent);
            $pageContent = str_replace('</style><link', "</style>\n\t<link", $pageContent);

            if ($pageMeta)
            {
                /** Insert title, meta keywords and meta description */
                $meta = '<title>' . $pageMeta->pages_title . '</title>' . "\r\n";
                $meta .= "\t".'<meta name="description" content="' . $pageMeta->pages_meta_description . '">' . "\r\n";
                $meta .= "\t".'<meta name="keywords" content="' . $pageMeta->pages_meta_keywords . '">';
                $pageContent = str_replace('<!--pageMeta-->', $meta, $pageContent);

                /** Insert header includes; */
                $includesPlusCss = '';
                if ($pageMeta->pages_header_includes != '')
                {
                    $includesPlusCss .= $pageMeta->pages_header_includes;
                }
                if ($pageMeta->pages_css != '')
                {
                    $includesPlusCss .= "\n\t<style>" . $pageMeta->pages_css . "</style>\n";
                }
                if ($theSite->global_css != '')
                {
                    $includesPlusCss .= "\n\t<style>" . $theSite->global_css . "</style>\n";
                }
                /** Insert header includes */
                $pageContent = str_replace('<!--headerIncludes-->', $includesPlusCss, $pageContent);
            }
            else
            {
                $pageContent = $content;
            }

            /** Remove frameCovers */
            $pageContent = str_replace('<div class="frameCover" data-type="video"></div>', "", $pageContent);

            /** This is needed for correct exports */
            $pageContent = str_replace('src="../', 'src="', $pageContent);
            $pageContent = str_replace("src='../", "src='", $pageContent);
            $pageContent = str_replace('url(../', 'url(', $pageContent);
            $pageContent = str_replace("url('../", "url('", $pageContent);
            $pageContent = str_replace('url("../', 'url("', $pageContent);
            $pageContent = str_replace('&quot;../bundles/', '&quot;bundles/', $pageContent);//FF needs this

            if ($this->config->item('google_api') !== '')
            {
                $pageContent = str_replace('</body>', '<script async defer src="https://maps.googleapis.com/maps/api/js?key=' . $this->config->item('google_api') . '&callback=initMap"></script>' . '</body>' . "\n", $pageContent);
            }

            /** setup of htmLawed helper */
            $this->load->helper('htmlawed');

            $config = array(
                'tidy' => '1t1n',
                'direct_nest_list' => 1,
                'keep_bad' => 0,
                'css_expression' => 1,
                'elements' => '*',
                'lc_std_val' => 0,
                'make_tag_strict' => 0,
                'no_deprecated_attr' => 0,
                'style_pass' => 1,
                'unique_ids' => 0,
                'parent' => 'body',
                'hook_tag' => 'my_tag_function'
            );

            /** simle html dom library */
            $this->load->library('Simple_html_dom');
            $raw = str_get_html($pageContent, true, true, DEFAULT_TARGET_CHARSET, false);

            /** body first */
            $body = $raw->find('body')[0];
            $sanitzedBody = htmLawed($body, $config);
            $raw->find('body')[0]->innertext = $sanitzedBody;

            /** extract CSS urls */
            foreach ($raw->find('link[rel="stylesheet"]') as $cssLink)
            {
                /** extract CSS link, no need for blob uhrls */
                if ( substr($cssLink->href, 0, 4) != 'blob' )
                {
                    $cssUrls[] = $cssLink->href;

                    /** extract images from CSS */
                    if ( substr($cssLink->href, 0, 4) != 'http' && substr($cssLink->href, 0, 2) != '//' )
                    {
                        $CSS = file_get_contents("./".$this->config->item('elements_dir')."/".$cssLink->href);

                        $re = '/url\(\s*[\'"]?(\S*\.(?:jpe?g|gif|png))[\'"]?\s*\)[^;}]*?/i';
                        if (preg_match_all($re, $CSS, $matches))
                        {
                            foreach ( $matches[1] as $img )
                            {
                                $pageImages[] = $img;
                            }

                        }
                    }

                    /** extract fonts from CSS */
                    $re = '/url\(([\'"]?.[^\'"]*\.(eot|woff|woff2|ttf)[\'"]?)\)/i';
                    if (preg_match_all($re, $CSS, $matches))
                    {
                        foreach ($matches[1] as $font)
                        {
                            $fonts[] = $font;
                        }
                    }
                }
            }

            /** extract JS urls */
            foreach ($raw->find('script[src]') as $jsLink)
            {
                $jsUrls[] = $jsLink->src;
            }

            /** extract <image> src */
            foreach ($body->find('img') as $image)
            {
                $pageImages[] = $image->src;
            }

            /** extract CSS background images */
            $re = '/url\(\s*[\'"]?(\S*\.(?:jpe?g|gif|png))[\'"]?\s*\)[^;}]*?/i';
            if (preg_match_all($re, str_replace("&quot;", "'", $raw), $matches))
            {
                foreach ($matches[1] as $img)
                {
                    $pageImages[] = $img;
                }
            }

            /** extract parallax data-image-src files */
            foreach ($body->find('*[data-parallax]') as $element)
            {
                $pageImages[] = $element->getAttribute('data-image-src');
            }

            /** last minute clean up of <script> tags */
            $raw = str_replace("\n\t\t \n\t</script>", "</script>", $raw);
            $raw = str_replace("\t</body>", "</body>", $raw);

            // die($raw);

            $zip->addFromString($page . ".html", $_POST['doctype'] . "\n" . stripslashes($raw));
            // echo $content;
        }

        /** add items in the $pageImages to the ZIP */
        $pageImages = array_unique($pageImages);

        if (count($pageImages) > 0)
        {
            foreach ($pageImages as $image)
            {
                $temp = explode('?', $image);

                $image = $temp[0];

                $info = new SplFileInfo('./img/exaple-image.jpg');

                if (substr($image, 0, 1) == '/')
                {
                    $image_ = "." . $image;
                }
                /** image located in the user upload folder */
                elseif (substr($image, 0, mb_strlen($this->config->item('images_uploadDir'))) == $this->config->item('images_uploadDir'))
                {
                    $image_ = "./" . $image;
                }
                /** stuff located in the bundles folder */
                else
                {
                    if (strpos($image, 'bundles') !== false)
                    {
                        $image_ = './'.$this->config->item('elements_dir').'/'.$image;
                    }
                    else
                    {
                        $image_ = './'.$image;
                    }

                }

                $info = new SplFileInfo($image_);

                /** Add current file to archive */
                if (file_exists($info->getRealPath()))
                {
                    $zip->addFile($info->getRealPath(), $image);
                }
            }
        }

        /** add items in the $cssUrls to the ZIP */
        $cssUrls = array_unique($cssUrls);

        if (count($cssUrls) > 0)
        {
            foreach ($cssUrls as $cssUrl)
            {
                $temp = explode('?', $cssUrl);

                $cssUrl = $temp[0];

                $cssUrl_ = './' . $this->config->item('elements_dir') . '/' . $cssUrl;

                $info = new SplFileInfo($cssUrl_);

                /** Add current file to archive */
                if (file_exists($info->getRealPath()))
                {
                    $zip->addFile($info->getRealPath(), $cssUrl);
                }
            }
        }

        /** add items in the $jsUrls to the ZIP */
        $jsUrls = array_unique($jsUrls);

        if (count($jsUrls) > 0)
        {
            foreach ($jsUrls as $jsUrl)
            {
                $temp = explode('?', $jsUrl);

                $jsUrl = $temp[0];

                $jsUrl_ = './' . $this->config->item('elements_dir') . '/' . $jsUrl;

                $info = new SplFileInfo($jsUrl_);

                /** Add current file to archive */
                if (file_exists($info->getRealPath()))
                {
                    $zip->addFile($info->getRealPath(), $jsUrl);
                }
            }
        }

        //die();

        /** Add folder structure */
        /** Prep path to assets array */
        /** Only web fonts are still added the old way */
        $temp = explode("|", $this->config->item('export_pathToAssets'));

        foreach ($temp as $thePath)
        {
            /** Create recursive directory iterator */
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($thePath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            foreach ($files as $name => $file)
            {
                if ($file->getFilename() != '.' && $file->getFilename() != '..' && (strpos($file->getFilename(), ".woff") || strpos($file->getFilename(), ".woff2") || strpos($file->getFilename(), ".ttf") || strpos($file->getFilename(), ".eot")))
                {
                    /** Get real path for current file */
                    $filePath = $file->getRealPath();

                    if (strpos($file,'images/') === FALSE)
                    {
                        $temp = explode("/", $name);
                        array_shift( $temp );
                        $newName = implode("/", $temp);
                    }
                    else
                    {
                        $newName = $name;
                    }

                    /** Add current file to archive */
                    $zip->addFile($filePath, $newName);
                }
            }
        }

        function my_tag_function($element, $attribute_array=0)
        {
            if (is_numeric($attribute_array))
            {
                if ($element == 'script')
                {
                    return "</$element>";
                }
                else
                {
                    return "</$element>";
                }
            }

            $string = '';
            foreach ($attribute_array as $k=>$v)
            {
                $string .= " {$k}=\"{$v}\"";
            }

            if ($element == 'script')
            {
                return "<{$element}{$string}> ";
            }
            else
            {
                return "<{$element}{$string}>";
            }
        }

        $zip->close();
        $yourfile = $this->config->item('export_fileName');
        $file_name = basename($yourfile);

        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Content-Length: " . filesize("./tmp/" . $yourfile));

        readfile("./tmp/" . $yourfile);

        unlink('./tmp/' . $yourfile);

        /** Hook point */
        $this->hooks->call_hook('sites_export_post');

        exit;
    }

    /**
     * Moves a single site to the trash bin
     *
     * @param   string      $site_id
     * @return  json        $return
     */
    public function trash($site_id = '')
    {
        /** Hook point */
        $this->hooks->call_hook('sites_trash_pre');

        if ($site_id == '' || $site_id == 'undefined')
        {
            $temp = array();
            $temp['header'] = $this->lang->line('sites_trash_no_site_error_heading');
            $temp['content'] = $this->lang->line('sites_trash_no_site_error_message');

            $this->return = array();
            $this->return['responseCode'] = 0;
            $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), TRUE);

            die(json_encode($this->return));
        }

        /** All good, move to trash */
        $this->MSites->trash($site_id);
        $data = array(
         'userid' => $this->session->userdata('user_id'),
         'username' =>$this->session->userdata('user_email'),
         'category' => "Sites Deleted",
         'description' => "User deleted ".$site_id." page",
         'ip_address' => $this->input->ip_address());
         $this->db->insert('tblactivitylog',$data);
        $temp = array();
        $temp['header'] = $this->lang->line('sites_trash_success_heading');
        $temp['content'] = $this->lang->line('sites_trash_success_message').'<script>$(\'#site_'.$site_id.'\').remove();</script>';

        $this->return = array();
        $this->return['responseCode'] = 1;
        $this->return['responseHTML'] = $this->load->view('shared/success', array('data'=>$temp), TRUE);

        /** Hook point */
        $this->hooks->call_hook('sites_trash_post');

        die(json_encode($this->return));
    }

    /**
     * Updates page meta data with ajax call
     *
     * @return  json        $return
     */
    public function updatePageData()
    {
        /** Hook point */
        $this->hooks->call_hook('sites_updatePageData_pre');

        if ($_POST['siteID'] == '' || $_POST['siteID'] == 'undefined' || ! isset($_POST))
        {
            $temp = array();
            $temp['header'] = $this->lang->line('sites_updatePageData_no_site_error_heading');
            $temp['content'] = $this->lang->line('sites_updatePageData_no_site_error_message');

            $this->return = array();
            $this->return['responseCode'] = 0;
            $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), TRUE);

            die(json_encode($this->return));
        }

        /** Update page data */
        $this->pagemodel->updatePageData($_POST);

        /** Return page data as well */
        $this->return = array();
        $pagesData = $this->pagemodel->getPageData($_POST['siteID']);
        if ($pagesData)
        {
            $this->return['pagesData'] = $pagesData;
        }

        $temp = array();
        $temp['header'] = $this->lang->line('sites_updatePageData_success_heading');
        $temp['content'] = $this->lang->line('sites_updatePageData_success_message');

        $this->return['responseCode'] = 1;
        $this->return['responseHTML'] = $this->load->view('shared/success', array('data'=>$temp), TRUE);

        /** Hook point */
        $this->hooks->call_hook('sites_updatePageData_post');

        die(json_encode($this->return));
    }

    /**
     * Function generates a live preview of current changes
     *
     * @return  void
     */
    public function livepreview()
    {
    //  echo 'test';
        /** Hook point */
        $this->hooks->call_hook('sites_livepreview_pre');

        if (isset($_POST['siteID']) && $_POST['siteID'] != '')
        {
            $siteData = $this->MSites->siteData($_POST['siteID']);
        }
      //  print_r($_REQUEST);
        $meta = '';
        /** Page title */
        if (isset($_POST['meta_title']) && $_POST['meta_title'] != '')
        {
            $meta .= '<title>' . $_POST['meta_title'] . '</title>' . "\n";
        }
        /** Page meta description */
        if (isset($_POST['meta_description']) && $_POST['meta_description'] != '')
        {
            $meta .= '<meta name="description" content="' . $_POST['meta_description'] . '"/>' . "\n";
        }
          $meta .= ' <base href="/elements/">';
        /** Page meta keywords */
        if (isset($_POST['meta_keywords']) && $_POST['meta_keywords'] != '')
        {
            $meta .= '<meta name="keywords" content="' . $_POST['meta_keywords'] . '"/>' . "\n";
        }
        /** Replace meta value */
        $content = str_replace('<!--pageMeta-->', $meta, "<!DOCTYPE html>\n" . $_POST['page']);

        /** Replace both inline css image url and image tag src */
        $content = str_replace('../bundles', 'bundles', $content);

        $head = '';
        /** Page header includes */
        if (isset($_POST['header_includes']) && $_POST['header_includes'] != '')
        {
            $head .= $_POST['header_includes'] . "\n";
        }
        /** Page css */
        if (isset($_POST['page_css']) && $_POST['page_css'] != '')
        {
            $head .= "\n<style>" . $_POST['page_css'] . "</style>\n";
        }
        /** Global css */
        if ($siteData->global_css != '')
        {
            $head .= "\n<style>" . $siteData->global_css . "</style>\n";
        }

        /** Custom header to deal with XSS protection */
        header("X-XSS-Protection: 0");

        /** Hook point */
        $this->hooks->call_hook('sites_livepreview_post');

        echo str_replace('<!--headerIncludes-->', $head, $content);
    }

    /**
     * Delete template
     *
     * @param   integer     $site_id
     * @param   integer     $page_id
     * @return  void
     */
    public function deltempl($site_id, $page_id)
    {
        /** Hook point */
        $this->hooks->call_hook('sites_deltempl_pre');

        if ($this->session->userdata('user_type') != "Admin")
        {
            die($this->lang->line('admin_permission_error'));
        }
        $this->MPages->deleteTemplate($site_id, $page_id);
        $return = array();
        $this->session->set_flashdata('success', $this->lang->line('sites_deltempl_delete_success'));

        /** Hook point */
        $this->hooks->call_hook('sites_deltempl_post');

        redirect('sites/' . $site_id, 'refresh');
    }

    /**
     * Attempts to retrieve a preview for a revision
     *
     * @param   string      $site_id
     * @param   string      $revisionStamp
     * @return  void
     */
    public function rpreview($site_id = '', $revisionStamp = '')
    {
        /** Hook point */
        $this->hooks->call_hook('sites_rpreview_pre');

        if ($site_id == '' || $revisionStamp == '' || $_GET['p'] == '')
        {
            die($this->lang->line('sites_rpreview_error'));
        }
        $page = $_GET['p'];
        $this->revisionOutput = $this->MRevisions->buildRevision($site_id, $revisionStamp, $page);

        /** Hook point */
        $this->hooks->call_hook('sites_rpreview_post');

        echo $this->revisionOutput;
    }

    /**
     * Updates revisions for a certain page with ajax call
     *
     * @param   string      $site_id
     * @param   string      $page
     * @return  void
     */
    public function getRevisions($site_id = '', $page = '')
    {
        /** Hook point */
        $this->hooks->call_hook('sites_getRevisions_pre');

        if ($site_id != '' && $page != '')
        {
            $this->revisions = $this->MRevisions->getForSite( $site_id, $page );

            /** Hook point */
            $this->hooks->call_hook('sites_getRevisions_post');

            $this->load->view('shared/revisions', array('revisions'=>$this->revisions, 'page'=>$page, 'siteID'=>$site_id));
        }
    }

    /**
     * Deletes a revision with ajax call
     *
     * @param   string      $site_id
     * @param   string      $timestamp
     * @param   string      $page
     * @return  json        $this->return
     */
    public function deleterevision($site_id = '', $timestamp = '', $page = '')
    {
        /** Hook point */
        $this->hooks->call_hook('sites_deleterevision_pre');

        $this->return = array();
        if ($site_id == '' || $timestamp == '' || $page == '')
        {
            $this->return['code'] = 0;
            $this->return['message'] = $this->lang->line('sites_deleterevision_delete_error');
            die(json_encode($this->return));
        }
        $this->MRevisions->delete($site_id, $timestamp, $page);
        $this->return['code'] = 1;
        $this->return['message'] = $this->lang->line('sites_deleterevision_delete_success');

        /** Hook point */
        $this->hooks->call_hook('sites_deleterevision_post');

        echo json_encode($this->return);
    }

    /**
     * Restores a revision for a specific page
     *
     * @param   string      $site_id
     * @param   string      $timestamp
     * @param   string      $page
     * @return  void
     */
    public function restorerevision($site_id = '', $timestamp = '', $page = '')
    {
        /** Hook point */
        $this->hooks->call_hook('sites_restorerevision_pre');

        if ($site_id == '' || $timestamp == '' || $page == '')
        {
            die($this->lang->line('sites_restorerevision_error'));
        }
        $this->MRevisions->restore($site_id, $timestamp, $page);

        /** Hook point */
        $this->hooks->call_hook('sites_restorerevision_post');

        redirect('sites/' . $site_id . "?p=" . $page, 'location');
    }

    /**
     * Loads a single page so a screenshot can be generated
     *
     * @param   integer     $page_id
     * @return  void
     */
    public function loadsinglepage($page_id)
    {
        die($this->MPages->load_page($page_id));
    }

    /**
     * Loads a single frame so a screenshot can be generated
     *
     * @param   integer     $frame_id
     * @return  void
     */
    public function loadsingleframe($frame_id)
    {
        die($this->MFrames->load_frame($frame_id));
    }

    /**
     * Connects FTP and returns a list of files/folders
     *
     * @return  json        $this->return
     */
    public function connect()
    {
        if ($this->input->post('ftp_type') == 'ftp')
        {
            $this->load->library('ftp');

            $config['hostname'] = $this->input->post('ftp_server');
            $config['username'] = $this->input->post('ftp_user');
            $config['password'] = $this->input->post('ftp_password');
            $config['port'] = $this->input->post('ftp_port');
            $config['debug'] = FALSE;

            if ($this->ftp->connect($config))
            {
                $path = ($this->input->post('ftp_path') != '') ? $this->input->post('ftp_path') : "/";

                $list = $this->ftp->list_files($path);

                if ($list)
                {
                    $temp = array();
                    $temp['list'] = $list;
                    $temp['data'] = $this->input->post();
                    $this->return = array();
                    $this->return['responseCode'] = 1;
                    $this->return['responseHTML'] = $this->load->view('shared/ftplist', array('data'=>$temp), true);
                    die(json_encode($this->return));
                }
                else
                {
                    $temp = array();
                    $temp['header'] = $this->lang->line('sites_connect_path_error_heading');
                    $temp['content'] = $this->lang->line('sites_connect_path_error_message');
                    $this->return = array();
                    $this->return['responseCode'] = 0;
                    $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), true);
                    die(json_encode($this->return));
                }
            }
            else
            {
                $temp = array();
                $temp['header'] = $this->lang->line('sites_connect_details_error_heading');
                $temp['content'] = $this->lang->line('sites_connect_details_error_message');
                $this->return = array();
                $this->return['responseCode'] = 0;
                $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), true);
                die(json_encode($this->return));
            }
        }
        else
        {
            $this->load->library('sftp');

            $config['hostname'] = $this->input->post('ftp_server');
            $config['username'] = $this->input->post('ftp_user');
            $config['password'] = $this->input->post('ftp_password');
            $config['port'] = $this->input->post('ftp_port');
            $config['debug'] = FALSE;

            if ($this->sftp->connect($config))
            {
                $path = ($this->input->post('ftp_path') != '') ? $this->input->post('ftp_path') : '/';

                $list = $this->sftp->list_files($path);

                if ($list)
                {
                    $temp = array();
                    $temp['list'] = $list;
                    $temp['data'] = $this->input->post();
                    $this->return = array();
                    $this->return['responseCode'] = 1;
                    $this->return['responseHTML'] = $this->load->view('shared/ftplist', array('data'=>$temp), true);
                    die(json_encode($this->return));
                }
                else
                {
                    $temp = array();
                    $temp['header'] = $this->lang->line('sites_connect_path_error_heading');
                    $temp['content'] = $this->lang->line('sites_connect_path_error_message');
                    $this->return = array();
                    $this->return['responseCode'] = 0;
                    $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), true);
                    die(json_encode($this->return));
                }
            }
            else
            {
                $temp = array();
                $temp['header'] = $this->lang->line('sites_connect_details_error_heading');
                $temp['content'] = $this->lang->line('sites_connect_details_error_message');
                $this->return = array();
                $this->return['responseCode'] = 0;
                $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), true);
                die(json_encode($this->return));
            }
        }

    }

    /**
     * Tests an FTP connection and verifies it's details
     *
     * @return  json        $this->return
     */
    public function test_ftp()
    {
        $path = ($this->input->post('ftp_path') != '') ? $this->input->post('ftp_path') : "/";

        $result = $this->MFtp->test($this->input->post('ftp_server'), $this->input->post('ftp_user'), $this->input->post('ftp_password'), $this->input->post('ftp_port'), $path, $this->input->post('ftp_type'));

        $this->return = array();

        /** All good */
        if ($result['connection'])
        {
            $temp = array();
            $temp['header'] = $this->lang->line('sites_test_success_heading');
            $temp['content'] = $this->lang->line('sites_test_success_message');
            $this->return['responseCode'] = 1;
            $this->return['responseHTML'] = $this->load->view('shared/success', array('data'=>$temp), true);
            die(json_encode($this->return));
        }
        else
        {
            /** Connection details failed */
            if ($result['problem'] == 'connection')
            {
                $temp = array();
                $temp['header'] = $this->lang->line('sites_test_error_details_heading');
                $temp['content'] = $this->lang->line('sites_test_error_details_message');
                $this->return['responseCode'] = 0;
                $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), true);
                die(json_encode($this->return));
            }
            /** Path failed */
            elseif ($result['problem'] == 'path')
            {
                $temp = array();
                $temp['header'] = $this->lang->line('sites_test_error_path_heading');
                $temp['content'] = $this->lang->line('sites_test_error_path_message');
                $this->return['responseCode'] = 0;
                $this->return['responseHTML'] = $this->load->view('shared/error', array('data'=>$temp), true);
                die(json_encode($this->return));
            }
        }
    }

    /**
     * Clone enitire site
     *
     * @param   integer     $site_id
     * @return  void
     */
    public function clone_site($site_id)
    {
        $site = $this->MSites->get_by_id($site_id);
        $site_name = $site['sites_name'] . ' - Clone';
        $pages = $this->MPages->get_all($site_id);
        $this->MSites->clone_site($site, $pages);

        $this->session->set_flashdata('success', $this->lang->line('sites_clone_confirm'));
        redirect('sites', 'refresh');
    }

    /**
     * Save favourite block
     *
     * @return array    $this->data
     */
    public function favourite_block()
    {
        if ($this->input->post())
        {
            /** Hook point */
            $this->hooks->call_hook('sites_favourite_block_pre');
            $this->data['return'] = [];

            $frame_id = $this->MFrames->insert_frames_as_fav($this->input->post());
            $block_id = $this->MBlocksFav->insert($frame_id, $this->input->post());
            $screenshot_url = base_url() . 'loadsingleframe/' . $frame_id;
            $filename = 'blockthumb_' . $block_id . '.jpg';

            $this->load->library('screenshot_library');
            $screenshot = $this->screenshot_library->make_screenshot($screenshot_url, $filename, $this->input->post('frames_width') . 'x' . $this->input->post('frames_height'), $this->config->item('screenshot_blockhumbs_folder'));

            if ($screenshot)
            {

                // resize the image
                $config['source_image'] = $this->config->item('screenshot_blockhumbs_folder') . $screenshot;
                $config['width'] = 520;

                $this->load->library('image_lib', $config);

                $this->image_lib->resize();

                $blockthumb = $this->config->item('screenshot_blockhumbs_folder') . $screenshot;
                $this->MBlocksFav->update_field($block_id, 'blocks_thumb', $blockthumb);

                $this->data['return']['block'] = $this->MBlocksFav->getsingle($block_id);

                $this->data['return']['responseCode'] = 1;

            }
            else
            {
                // screenshot failed, remove the frame and fav block
                $this->MFrames->delete_frame($frame_id);
                $this->MBlocksFav->delete($block_id);

                $this->data['return']['responseCode'] = 0;
            }

            /** Hook point */
            $this->hooks->call_hook('sites_favourite_block_post');

            die(json_encode($this->data['return']));
        }
    }

    /**
     * Delete favourite block
     *
     * @param   integer         $block_id
     * @return  void
     */
    public function favourite_block_del($block_id)
    {
        /** Hook point */
        $this->hooks->call_hook('sites_favourite_block_del_pre');

        $this->MBlocksFav->delete($block_id);

        $this->data['return']['responseCode'] = 1;

        /** Hook point */
        $this->hooks->call_hook('sites_favourite_block_del_post');

        die(json_encode($this->data['return']));

    }

    /**
     * Controller desctruct method for custom hook point
     *
     * @return  void
     */
    public function __destruct()
    {
        /** Hook point */
        $this->hooks->call_hook('sites_destruct');
    }

}
