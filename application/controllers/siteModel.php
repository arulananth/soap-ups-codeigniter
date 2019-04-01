<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sites_model extends CI_Model {

    /**
     * Class constructor
     *
     * @return  void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get site details by ID
     *
     * @param   integer     $id
     * @return  array       $data
     */
    public function get_by_id($id)
    {
        $data = [];
        $this->db->where('sites_id', $id);
        $this->db->where('sites_trashed', 0);
        $query = $this->db->get('sites');
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
                $data = $row;
            }
        }

        $query->free_result();
        return $data;
    }
    public function getUserActivityLog($limit, $start){

    	       $sql = 'select * from tblactivitylog WHERE `userid` != "4" ORDER BY `tblactivitylog`.`id`  DESC limit '.$start.', '.$limit.'';
              $query = $this->db->query($sql);
              return $query->result();
           }
    /**
     * Get site details by any field value
     *
     * @param   string      $field
     * @param   string      $value
     * @return  array       $data
     */
    public function get_by_field_value($field, $value)
    {
        $data = [];
        $this->db->where($field, $value);
        $this->db->where('sites_trashed', 0);
        $query = $this->db->get('sites');
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
                $data[] = $row;
            }
        }

        $query->free_result();
        return $data;
    }




    




    /**
     * Returns all available sites
     *
     * @param   integer     $user_id
     * @return  array       $allSites
     */
    public function all($user_id = '')
    {
    	/** If $user_id is set, this means we're looking for the sites belonging to a specific user */
    	if ($user_id == '')
        {
            if ($this->session->userdata('user_type') != 'Admin')
            {
                $this->db->where('users_id', $this->session->userdata('user_id'));
            }
        }
        else
        {
            $this->db->where('users_id', $user_id);
        }

        $this->db->from('sites');
        $this->db->where('sites_trashed', 0);
        $this->db->join('users', 'sites.users_id = users.id');
        $query = $this->db->get();
        $res = $query->result();

        /** Array holding all sites and associated data */
        $allSites = array();

        foreach ($res as $site)
        {
            $temp = array();
            $temp['siteData'] = $site;

            /** Get the number of pages */
            $query = $this->db->from('pages')->where('sites_id', $site->sites_id)->get();
            $res = $query->result();

            $temp['nrOfPages'] = $query->num_rows();

          //  $this->db->flush_cache();

            /** Grab the first frame for each site, if any */
            $q = $this->db->from('pages')->where('pages_name', 'index')->where('sites_id', $site->sites_id)->get();

            if ($q->num_rows() > 0)
            {
                $res = $q->result();
                $indexPage = $res[0];

                $q = $this->db->from('frames')->where('pages_id', $indexPage->pages_id)->where('revision', 0)->order_by('frames_id', 'asc')->limit(1)->get();

                if ($q->num_rows() > 0)
                {
                    $res = $q->result();
                    $temp['lastFrame'] = $res[0];
                }
                else
                {
                    $temp['lastFrame'] = '';
                }
            }
            else
            {
                $temp['lastFrame'] = '';
            }

            $allSites[] = $temp;
        }

        return $allSites;
    }

    /**
     * Site count by user
     *
     * @param   integer     $user_id
     * @return  array       $query
     */
    public function site_by_user($user_id)
    {
        $this->db->where('users_id', $user_id);
        $this->db->where('sites_trashed', 0);
        $this->db->from('sites');
        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * Checks to see if a site belongs to this user
     *
     * @param   integer     $siteID
     * @return  boolean
     */
    public function isMine($site_id)
    {
    	$user_id = $this->session->userdata('user_id');
    	$q = $this->db->from('sites')->where('sites_id', $site_id)->get();
    	if ($q->num_rows() > 0)
        {
            $res = $q->result();
            if ($res[0]->users_id != $user_id)
            {
                return FALSE;
            }
            else
            {
                return TRUE;
            }
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Creates a new, empty shell site
     *
     * @return  integer     $new_site_id;
     */
    public function createNew()
    {

    	$user_id = $this->session->userdata('user_id');
    	/** Create site */
    	$data = array(
            'sites_name'        => 'My New Site',
            'users_id'          => $user_id,
            'sites_created_on'  => time(),
            'created_at'        => date("Y-m-d H:i:s")
        );
    	$this->db->insert('sites', $data);

    	$new_site_id = $this->db->insert_id();

    	/** Create empty index page */
        $data = array(
            'sites_id'          => $new_site_id,
            'pages_name'        => 'index',
            'pages_timestamp'   => time(),
            'created_at'        => date("Y-m-d H:i:s")
        );
        $this->db->insert('pages', $data);

        $data = array(
                         'userid' => $this->session->userdata('user_id'),
                         'username' =>$this->session->userdata('user_email'),
                         'category' => "Created New Site",
                         'description' => 'User created new site. Site Name: [My New Site], Site Url: [http://mywebbiy.com/sites/'.$new_site_id.'].',

                         'ip_address' => $this->input->ip_address());

                          $this->db->insert('tblactivitylog',$data);

        return $new_site_id;

    }

    /**
     * Creates a new site item, including pages and frames
     *
     * @param   string      $site_name
     * @param   array       $pages
     * @return  integer     $site_id
     */
    public function create($site_name, $pages)
    {
    	$user_id = $this->session->userdata('user_id');

    	/** Create the site item first */
        $data = array(
            'users_id'          => $user_id,
            'sites_name'        => $site_name,
            'sites_created_on'  => time(),
            'created_at'        => date("Y-m-d H:i:s")
        );
        $this->db->insert('sites', $data);
        $site_id = $this->db->insert_id();
        $data = array(
                               'userid' => $this->session->userdata('user_id'),
                               'username' =>$this->session->userdata('user_email'),
                               'category' => "Created New Site",
                               'description' => "User created new site. Site Name: [My New Site], Site Url: [http://mywebbiy.com/sites/'.$site_id.'] ",

                               'ip_address' => $this->input->ip_address());

                                $this->db->insert('tblactivitylog',$data);


        /** Next we create the pages and frames */
        foreach ($pages as $page_name => $frames)
        {
            $data = array(
                'sites_id'          => $site_id,
                'pages_name'        => $page_name,
                'pages_timestamp'   => time(),
                'created_at'        => date("Y-m-d H:i:s")
            );
            $this->db->insert('pages', $data);

            $page_id = $this->db->insert_id();

            /** Page is done, now all the frames for this page */
            foreach ($frames as $frame_data)
            {
                $data = array(
                    'pages_id'              => $page_id,
                    'sites_id'              => $site_id,
                    'frames_content'        => $frame_data['frameContent'],
                    'frames_height'         => $frame_data['frameHeight'],
                    'frames_original_url'   => $frame_data['originalUrl'],
                    'frames_sandbox'        => $frame_data['frameSandbox'],
                    'frames_loaderfunction' => $frame_data['frameLoaderfunction'],
                    'frames_timestamp'      => time(),
                    'created_at'            => date("Y-m-d H:i:s")
                );
                $this->db->insert('frames', $data);
            }
        }

        return $site_id;
    }

    /**
     * Clone an existing site including pages and frames
     *
     * @param   array       $site
     * @param   array       $pages
     * @return  integer     $site_id
     */
    public function clone_site($site, $pages)
    {
        $user_id = $this->session->userdata('user_id');

        /** Create the site item first */
        $data = array(
            'users_id'          => $user_id,
            'sites_name'        => $site['sites_name'] . ' - Clone',
            'sites_created_on'  => time(),
            'sitethumb'         => $site['sitethumb'],
            'created_at'        => date("Y-m-d H:i:s")
        );
        $this->db->insert('sites', $data);
        $site_id = $this->db->insert_id();

        /** Next we create the pages and frames */
        foreach ($pages as $page_name => $frames)
        {
            $data = array(
                'sites_id'          => $site_id,
                'pages_name'        => $page_name,
                'pages_timestamp'   => time(),
                'created_at'        => date("Y-m-d H:i:s")
            );
            $this->db->insert('pages', $data);
            $page_id = $this->db->insert_id();

            /** Page is done, now all the frames for this page */
            /** Check if the page has any frames */
            if (count($frames['frames']) > 0)
            {
                foreach ($frames['frames'] as $frame_data)
                {
                    $data = array(
                        'pages_id'              => $page_id,
                        'sites_id'              => $site_id,
                        'frames_content'        => $frame_data['frames_content'],
                        'frames_height'         => $frame_data['frames_height'],
                        'frames_original_url'   => $frame_data['frames_original_url'],
                        'frames_sandbox'        => $frame_data['frames_sandbox'],
                        'frames_loaderfunction' => $frame_data['frames_loaderfunction'],
                        'frames_timestamp'      => time(),
                        'created_at'            => date("Y-m-d H:i:s")
                    );
                    $this->db->insert('frames', $data);
                }
            }
        }

        return $site_id;
    }
    public function createNewtemplateSite()
      {
      	$user_id = $this->session->userdata('user_id');

      	// Create site
      	$data = array(
              'sites_name'        => 'My New Site',
              'users_id'          => $user_id,
              'sites_created_on'  => time()
              );
      	$this->db->insert('sites', $data);

      	$new_site_id = $this->db->insert_id();

      	// Create empty index page
          $data = array(
              'sites_id'          => $new_site_id,
              'pages_name'        => 'index',
              'pages_timestamp'   => time()
              );
          $this->db->insert('pages', $data);

  		$new_page_id = $this->db->insert_id();

          return $new_site_id.'-'.$new_page_id ;
      }
      public function savePageTemplateToSite($siteid,$pageid,$tmpltpageid)
	{
		$query = $this->db->from('frames')->where('pages_id', $tmpltpageid)->get();
		$listd = $query->result_array();

		foreach ($listd as $row)
		{
			$data = array(
				'pages_id' 				=> $pageid,
				'sites_id'				=> $siteid,
				'frames_content' 		=> $row['frames_content'],
				'frames_height' 		=> $row['frames_height'],
				'frames_original_url' 	=> $row['frames_original_url'],
				'frames_sandbox' 		=> 0,
				'frames_timestamp' 		=> time()
			);
			$datass = $this->db->insert('frames', $data);

		}
		return $this->db->insert_id();
		die;
	}

    /**
     * Updates an existing site item, including pages and frames
     *
     * @param   array       $siteData
     * @param   array       $pages
     * @return  void
     */
    public function update($siteData, $pages)
    {
        /** Update the site details first */
        $data = array(
            'sites_name'            => $siteData['sites_name'],
            'sites_lastupdate_on'   => time(),
            'viewmode'              => $siteData['responsiveMode']
        );
        $this->db->where('sites_id', $siteData['sites_id']);
        $this->db->update('sites', $data);
        $data = array(
                            'userid' => $this->session->userdata('user_id'),
                            'username' =>$this->session->userdata('user_email'),
                            'category' => "Update Site Data",
                            'description' => "User update site data.",
                            //'description' => "User update site data. Site name : ['. $siteData["sites_name"].']",

                            'ip_address' => $this->input->ip_address());

                             $this->db->insert('tblactivitylog',$data);
        /** Update the pages */
        foreach ($pages as $page => $pageData)
        {
            /** Dealing with a changed page */
            if ($pageData['status'] == 'changed')
            {
                if ( ! isset($pageData['pageID']) || $pageData['pageID'] == 0)
                {
                    $query = $this->db->from('pages')->where('sites_id', $siteData['sites_id'])->where('pages_name', $page)->get();
                    $pageDataOld = $query->result();
                    $pageID = $pageDataOld[0]->pages_id;
                }
                else
                {
                    $pageID = $pageData['pageID'];
                }

                $data = array(
                    'pages_name'                => $page,
                    'pages_timestamp'           => time(),
                    'pages_title'               => $pageData['pageSettings']['title'],
                    'pages_meta_keywords'       => $pageData['pageSettings']['meta_keywords'],
                    'pages_meta_description'    => $pageData['pageSettings']['meta_description'],
                    'pages_header_includes'     => $pageData['pageSettings']['header_includes'],
                    'pages_css'                 => $pageData['pageSettings']['page_css']
                );
                $this->db->where('pages_id', $pageID);
                $this->db->update('pages', $data);
            }
            elseif ($pageData['status'] == 'new')
            {
                $data = array(
                    'sites_id'                  => $siteData['sites_id'],
                    'pages_name'                => $page,
                    'pages_timestamp'           => time(),
                    'pages_title'               => $pageData['pageSettings']['title'],
                    'pages_meta_keywords'       => $pageData['pageSettings']['meta_keywords'],
                    'pages_meta_description'    => $pageData['pageSettings']['meta_description'],
                    'pages_header_includes'     => $pageData['pageSettings']['header_includes'],
                    'pages_css'                 => $pageData['pageSettings']['page_css']
                );
                $this->db->insert('pages', $data);
                $pageID = $this->db->insert_id();
            }

            /** Page done, onto the blocks */
            /** Push existing frames into revision */
            $data = array(
                'revision' => 1
            );
            $this->db->where('pages_id', $pageID);
            $this->db->update('frames', $data);

            if (isset($pageData['blocks']))
            {
            	foreach ($pageData['blocks'] as $block)
                {
                	$data = array(
                        'pages_id'              => $pageID,
                        'sites_id'              => $siteData['sites_id'],
                        'frames_content'        => $this->processFrameContent($block['frameContent']),
                        'frames_height'         => $block['frameHeight'],
                        'frames_original_url'   => $block['originalUrl'],
                        'frames_sandbox'        => ($block['sandbox'] == 'TRUE') ? 1 : 0,
                        'frames_loaderfunction' => $block['loaderFunction'],
                        'frames_timestamp'      => time(),
                        'frames_global'         => (isset($block['frames_global']))? 1: 0,
                    );

                	$this->db->insert('frames', $data);
                }
            }

            /** Screenshot of index page */
            if ($page == 'index')
            {
                $screenshotUrl = base_url() . 'loadsinglepage/' . $pageID;
                $filename = 'sitethumb_' . $siteData['sites_id'] . '.jpg';

                $this->load->library('screenshot_library');
                $screenshot = $this->screenshot_library->make_screenshot($screenshotUrl, $filename, '520x440', $this->config->item('screenshot_sitethumbs_folder'));

                if ($screenshot)
                {
                    $data = array(
                        'sitethumb' => $this->config->item('screenshot_sitethumbs_folder') . $screenshot
                    );
                    $this->db->where('sites_id', $siteData['sites_id']);
                    $this->db->update('sites', $data);
                }

            }

        }
    }

    /**
     * Updates a site's meta data (name, ftp details, etc)
     *
     * @param   string      $frameContent
     * @return  string      $raw
     */
    public function processFrameContent($frameContent)
    {
        $this->load->library('Simple_html_dom');

        $raw = str_get_html($frameContent, true, true, DEFAULT_TARGET_CHARSET, false);

        /** remove data-selector attributes */
        foreach($raw->find('*[data-selector]') as $element)
        {
            /** remove attribute */
            $element->removeAttribute("data-selector");
        }

        /** remove draggable attributes */
        foreach($raw->find('*[draggable]') as $element)
        {
            $element->removeAttribute("draggable");
        }

        /** remove builder scripts (these are injected when loading the iframes) */
        foreach($raw->find('script.builder') as $element)
        {
            $element->outertext = '';
        }

        /** remove background images for parallax blocks */
        foreach($raw->find('*[data-parallax]') as $element)
        {
            $oldCss = $element->getAttribute('style');
            $replaceWith = "background-image: none";

            $regex = '/(background-image: url\((["|\']?))(.+)(["|\']?\))/';

            $oldCss = preg_replace($regex, $replaceWith, $oldCss);

            $element->setAttribute('style', $oldCss);
        }

        return $raw;
    }

    /**
     * Set home page flag 0 to all sites
     *
     * @return  boolean
     */
    public function remove_home_page()
    {
        $data = array('home_page' => 0);
        $this->db->update('sites', $data);

        if ($this->db->affected_rows() >= 0)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Updates a site's meta data (name, ftp details, etc)
     *
     * @param   array       $site_data
     * @return  boolean
     */
    public function updateSiteData($site_data)
    {
       
        $sites_data=$this->get_by_id($site_data["siteID"]);
        $site_data=array_merge($site_data,$sites_data);
       
        if($site_data["sites_name"]=='') $site_data["sites_name"]=$site_data["custom_domain"];
        $site_data['siteSettings_siteName']=$site_data["sites_name"];
      $data = array(
                       'userid' => $this->session->userdata('user_id'),
                       'username' =>$this->session->userdata('user_email'),
                       'category' => "Published Site",
                       'description' => 'User Published Site. Site Name: ['.$site_data['siteSettings_siteName'].'], Site Url(Sub Folder): [http://mywebbiy.com/sites/'.$site_data['sub_folder'].']',
                       'ip_address' => $this->input->ip_address());

                        $this->db->insert('tblactivitylog',$data);
        $this->load->model('shared/Ftp_model', 'MFtp');
        $site_data = array_map('trim', $site_data);
        /** test the FTP data */
        $ftp_ok = 0;
        //if (isset($site_data['ftp_type']))
      //  {
      //      $path = ($site_data['ftp_path'] != '') ? $site_data['ftp_path'] : "/";
      //      $result = $this->MFtp->test($site_data['ftp_server'], $site_data['ftp_user'], $site_data['ftp_password'], $site_data['ftp_port'], $path, $site_data['ftp_type']);
//
      //      if ($result['connection'])
      //      {
      //          $ftp_ok = 1;
    //        }
    //    }



        $data = array(
            'sites_name'    => $site_data['sites_name'],
            'custom_domain' => isset($site_data['custom_domain']) ? $site_data['custom_domain'] : '',
            'sub_domain'    => isset($site_data['sub_domain']) ? $site_data['sub_domain'] : '',
            'sub_folder'    => NULL,
            'home_page'     => isset($site_data['home_page']) ? $site_data['home_page'] : '',
          'ftp_type'      => NULL,
          'ftp_server'    => NULL,
            'ftp_user'      => NULL,
            'ftp_password'  => NULL,
            'ftp_path'      => NULL,
            'ftp_port'      => NULL,
            'ftp_ok'        => NULL,
            'sites_trashed' => 0,
            'global_css'    => $site_data['global_css'],
            'remote_url'    => isset($site_data['remote_url']) ? $site_data['remote_url'] : ''
        );

        $this->db->where('sites_id', $site_data['siteID']);
        $this->db->update('sites', $data);
        $sites_data=$this->get_by_id($site_data["siteID"]);
        //print_r($data);
        if ($this->db->affected_rows() >= 0)
        {

            $return['return'] = 'test';
            $return['responseHTML'] = 'test';
            return $return;
        }
        else
        {

            $return['return'] = 'test';
            $return['responseHTML'] = 'test';
            return $return;
        }
    }

    /**
     * Returns a single site, without pages/frames
     *
     * @param   integer     $site_id
     * @return  mixed       $res[0]/FALSE
     */
    public function siteData($site_id)
    {
        $query = $this->db->from('sites')->where('sites_id', $site_id)->get();
        if ($query->num_rows() > 0)
        {
            $res = $query->result();
            return $res[0];
        }
        else
        {
            return FALSE;
        }
    }


    /**
     * Takes a site ID and returns all the site data, or FALSE is the site doesn't exist
     *
     * @param   integer     $site_id
     * @return  mixed       $siteArray/FALSE
     */
    public function getSite($site_id)
    {
    	$query = $this->db->from('sites')->where('sites_id', $site_id)->get();
    	if ($query->num_rows() == 0)
        {
            return FALSE;
        }

        $res = $query->result();

        $site = $res[0];

        $siteArray = array();
        $siteArray['site'] = $site;

        /** Get the pages + frames */
        $query = $this->db->from('pages')->where('sites_id', $site->sites_id)->get();
        $res = $query->result();
        $pageFrames = array();
        foreach ($res as $page)
        {
            /** Get the frames for each page */
            $query = $this->db->from('frames')->where('pages_id', $page->pages_id)->where('revision', 0)->order_by('frames_id')->get();

            $pageDetails = array();
            $pageDetails['blocks'] = $query->result();
            $pageDetails['page_id'] = $page->pages_id;
            $pageDetails['pages_title'] = $page->pages_title;
            $pageDetails['meta_description'] = $page->pages_meta_description;
            $pageDetails['meta_keywords'] = $page->pages_meta_keywords;
            $pageDetails['header_includes'] = $page->pages_header_includes;
            $pageDetails['page_css'] = $page->pages_css;

            $pageFrames[$page->pages_name] = $pageDetails;
        }

        $siteArray['pages'] = $pageFrames;

        /** Grab the assets folders as well */
        $this->load->helper('directory');

        $folderContent = directory_map($this->config->item('elements_dir'), 2);
        $assetFolders = array();

        if (is_array($folderContent))
        {
            foreach ($folderContent as $key => $item)
            {
                if (is_array($item))
                {
                    array_push($assetFolders, $key);
                }
            }
        }

        $siteArray['assetFolders'] = $assetFolders;

        /** Site package options for user */
        if ($this->session->userdata('user_type') != "Admin")
        {
            $package = $this->MPackages->get_by_id($this->session->userdata('package_id'));
            $siteArray['hosting_option'] = json_decode($package['hosting_option']);
            $siteArray['ftp_publish'] = $package['ftp_publish'];
        }

        return $siteArray;
    }

    /**
     * Grabs a single frame and returns it
     *
     * @param   integer     $frame_id
     * @return  array       $res[0]
     */
    public function getSingleFrame($frame_id)
    {
        $query = $this->db->from('frames')->where('frames_id', $frame_id)->get();
        $res = $query->result();
        return $res[0];
    }

    /**
     * Gets the assets and pages of a site
     *
     * @param   integer     $site_id
     * @return  array       $return
     */
    public function getAssetsAndPages($site_id)
    {
        /** Get the asset folders first, we only grab the first level folders inside $this->config->item('elements_dir') */
        $this->load->helper('directory');

        $folderContent = directory_map($this->config->item('elements_dir'), 2);
        $assetFolders = array();

        foreach ($folderContent as $key => $item)
        {
            if (is_array($item))
            {
                array_push($assetFolders, $key);
            }
        }

        /** Now we get the pages */
        $query = $this->db->from('pages')->where('sites_id', $site_id)->get();
        $pages = $query->result();

        $return = array();
        $return['assetFolders'] = $assetFolders;
        $return['pages'] = $pages;

        return $return;
    }

    /**
     * Moves a site to the trash
     *
     * @param   integer     $site_id
     */
    public function trash($site_id)
    {
    	$data = array(
            'sites_trashed' => 1
        );

    	$this->db->where('sites_id', $site_id);
    	$this->db->update('sites', $data);

        if ($this->db->affected_rows() >= 0)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Returns all admin images
     *
     * @return  mixed       $adminImages/FALSE
     */
    public function adminImages()
    {
        $folderContent = directory_map($this->config->item('images_dir'), 2);

        if ($folderContent)
        {
            $adminImages = array();
            foreach ($folderContent as $key => $item)
            {
                if ( ! is_array($item))
                {
                    /** check the file extension */
                    $ext = pathinfo($item, PATHINFO_EXTENSION);
                    /** prep allowed extensions array */
                    $temp = explode("|", $this->config->item('images_allowedExtensions'));

                    if (in_array($ext, $temp))
                    {
                        array_push($adminImages, $item);
                    }
                }
            }

            return $adminImages;
        }
        else
        {
            return FALSE;
        }
    }

    public function siteImages($id)
    {
        $dir = 'images/sites/'.$id; //dir

        if (!is_dir($dir)) { //if not exist
            mkdir($dir, 0777, TRUE);
            $folderContent = directory_map($dir, 2);
        } else { //if exist
            $folderContent = directory_map($dir, 2);
        }


        if ($folderContent)
        {
          $adminImages = array();
          foreach ($folderContent as $key => $item)
          {
              if ( ! is_array($item))
              {
                  /** check the file extension */
                  $ext = pathinfo($item, PATHINFO_EXTENSION);
                  /** prep allowed extensions array */
                  $temp = explode("|", $this->config->item('images_allowedExtensions'));

                  if (in_array($ext, $temp))
                  {
                      array_push($adminImages, $item);
                  }
              }
          }

          return $adminImages;
        }
        else
        {
          return FALSE;
        }
    }

    /**
     * Trashes a users' sites
     *
     * @param   integer     $user_id
     */
    public function deleteAllFor($user_id)
    {
        $data = array(
            'sites_trashed' => 1
        );

        $this->db->where('users_id', $user_id);
        $this->db->update('sites', $data);
    }

    public function changeOwner($user_id,$site_id)
    {
        $data = array(
            'users_id' => $user_id
        );

        $this->db->where('sites_id', $site_id);
        $this->db->update('sites', $data);
    }

    /**
     * Grabs a singlepage for preview
     *
     * @param   integer     $site_id
     * @param   string      $page_name
     * @return  mixed       $q/FALSE
     */
    public function getPage($site_id, $page_name)
    {
        $q = $this->db->from('pages')->where('sites_id', $site_id)->where('pages_name', $page_name)->order_by('pages_timestamp', 'asc')->get()->row();

        if ($q->pages_preview != '')
        {
            return $q;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Marks site as published
     *
     * @param   integer     $site_id
     */
    public function published($site_id)
    {
        $data = array(
            'ftp_published'     => 1,
            'publish_date'      => time()
        );

        $this->db->where('sites_id', $site_id);
        $this->db->update('sites', $data);
    }

}
