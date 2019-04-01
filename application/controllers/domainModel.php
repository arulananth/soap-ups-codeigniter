<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Domain_model extends CI_Model {
    private $apikey = 'dacb8f5147f3df378fc92abc1bd47e44e97dd';
    private $user = 'webbiyinc@gmail.com';
    private $webbiyip = '54.39.99.157';
    private $cloud_url='';
    /**
     * Class constructor
     *
     * @return  void
     */
    public function __construct()
    {
        parent::__construct();
        include str_replace("ci_3.1.2/","application/",BASEPATH).'vendor/autoload.php';
        
    }


    public function setDKIM($domain,$dkim) {

       

        $key     = new Cloudflare\API\Auth\APIKey($this->user, $this->apikey);
        $adapter = new Cloudflare\API\Adapter\Guzzle($key);
        $user    = new Cloudflare\API\Endpoints\User($adapter);
        $dns = new \Cloudflare\API\Endpoints\DNS($adapter);
        $zones = new \Cloudflare\API\Endpoints\Zones($adapter);

        $zoneID = $zones->getZoneID($domain);
        $x = $dns->listRecords($zoneID);
        foreach($x->result as $key => $r) {
          if($r->type == 'TXT') {
            if($r->name == 'mail._domainkey.'.$domain || $r->name == '_domainkey.'.$domain || $r->name == $domain) {
              $dns->deleteRecord($zoneID,$r->id);
            }
          }
          if($r->type == 'MX') {
            $dns->deleteRecord($zoneID,$r->id);
          }
        }
        $dns->addRecord($zoneID, "TXT", 'mail._domainkey', '"k=rsa; p='.$dkim.'"',0,false);
        $dns->addRecord($zoneID, "MX", '@', 'mysiteupp.com', 0, false);
        $dns->addRecord($zoneID, "TXT", '_domainkey', '"t=y; o=~;"', 0, false);
        $dns->addRecord($zoneID, "TXT", $domain, '"v=spf1 a ip4:'.$this->webbiyip.' -all"', 0, false);

        return 1;
    }




    public function checkStatus($domain) {
     

      $key     = new Cloudflare\API\Auth\APIKey($this->user, $this->apikey);
      $adapter = new Cloudflare\API\Adapter\Guzzle($key);
      $user    = new Cloudflare\API\Endpoints\User($adapter);
      $dns = new \Cloudflare\API\Endpoints\DNS($adapter);
      $zones = new \Cloudflare\API\Endpoints\Zones($adapter);
      $check = false;
      try {
          $check = $zones->listZones($domain);
      } catch (Exception $e) {
          $msg = $e->getMessage();
      }
      if($check) {
        return $check->result[0]->status;
      } else {
        return false;
      }

    }




    public function getUserDomains() {
      $data = [];
      $user_id = $this->session->userdata('user_id');
      $this->db->select('*');
      $this->db->from('domains');
      $this->db->where('user', $user_id);
      $q = $this->db->get();


      if ($q->num_rows() > 0)
  		{
  				foreach ($q->result_array() as $row)
  				{
              $d = [];

              $cloudflare = $this->MDomain->check($row['domain']);

              $mailacc = $this->MVesta->getMailAccounts($row['domain']);
              $d['mail']['count'] = count($mailacc);

        			if($cloudflare) {
                $d['cf']['id'] = $cloudflare;
        			  $d['cf']['status'] = ucfirst($this->MDomain->checkStatus($row['domain']));
        			} else {
        	      $d['cf'] = false;
        			}

              $d['site']['id'] = $row['site'];
              $d['site']['name'] = $this->MSites->get_by_id($row['site'])['sites_name'];

  						$data[$row['domain']] = $d;
  				}

          $q->free_result();

          return $data;
  		} else {
        $q->free_result();
        return false;
      }



    }






    public function check($domain) {

      

      $key     = new Cloudflare\API\Auth\APIKey($this->user, $this->apikey);
      $adapter = new Cloudflare\API\Adapter\Guzzle($key);
      $user    = new Cloudflare\API\Endpoints\User($adapter);
      $dns = new \Cloudflare\API\Endpoints\DNS($adapter);
      $zones = new \Cloudflare\API\Endpoints\Zones($adapter);
      $msg = 'x';
      try {
          $zoneID = $zones->getZoneID($domain);
      } catch (Exception $e) {
          $msg = $e->getMessage();
      }
      if($msg = 'x') {
        return $zoneID;
      } else {
        return false;
      }

    }


    public function getCloudflareDomains() {
           

            $key     = new Cloudflare\API\Auth\APIKey($this->user, $this->apikey);
            $adapter = new Cloudflare\API\Adapter\Guzzle($key);
            $user    = new Cloudflare\API\Endpoints\User($adapter);
            $dns = new \Cloudflare\API\Endpoints\DNS($adapter);
            $zones = new \Cloudflare\API\Endpoints\Zones($adapter);
            $msg = 'x';
            try {
                $x = $zones->listZones();
            } catch (Exception $e) {
                $msg = $e->getMessage();
            }
            if($msg = 'x') {
                $xe = [];

              foreach ($x->result as $key => $domain) {
                $d = [];

                $d['id'] = $domain->id;
                $d['name'] = $domain->name;
                $d['status'] = $domain->status;
                $d['created_on'] = $domain->created_on;

                $xe[$domain->name] = $d;
              }
              return $xe;
            } else {
              return false;
            }

    }
    public function import_domain($domain,$dkim=false)
    {
         $rtn = [];
         $domain_data=dns_get_record($domain,DNS_NS);
         if(count($domain_data)==0) $rtn['error'] = 'Invalid domain';
         else
         {
              $rtn['ns_table']='';
              $rtn['ns_header']='';
              $rtn['ns']=array();
              $k=0;
             foreach($domain_data as $d)
             {
                 if($d["type"]=="NS")
                 {
                     $rtn['ns']['name_'.$k]=$d["target"];
                     $k++;
                 }
             }
             $rtn['ns_table'] = '<table class="table" width="100%"><thead><tr><td>Current</td><td>New</td></tr><tr><td>'.$rtn['ns']['name_0'].'</td><td>'.$rtn['ns']['name_1'].'</td></tr></table>';
                    $rtn['ns_header'] = 'Change NS records for '.$domain.' (Active)';
         }
         
         return $rtn;
    }

    public function import_domain_r($domain,$dkim=false) {
      
     
    
      //config

      $rtn = [];
      //require 'vendor/autoload.php';
      //api variables
      $key     = new \Cloudflare\API\Auth\APIKey($this->user, $this->apikey);
      $adapter = new Cloudflare\API\Adapter\Guzzle($key);
      $user    = new \Cloudflare\API\Endpoints\User($adapter);
      $dns = new \Cloudflare\API\Endpoints\DNS($adapter);
      $zones = new \Cloudflare\API\Endpoints\Zones($adapter);


      if(dns_get_record($domain, DNS_A + DNS_AAAA + DNS_CNAME + DNS_NS + DNS_MX + DNS_TXT)) { //domain ok

                    //add domain to Cloudflare
                    $x = $zones->addZone($domain, false);

                    if($x) {


                    $zoneID = $zones->getZoneID($domain);

                    $rtn['ns_table'] = '<table class="table" width="100%"><thead><tr><td>Current</td><td>New</td></tr><tr><td>'.$x->original_name_servers[0].'</td><td>'.$x->name_servers[0].'</td></tr>'.'<tr><td>'.$x->original_name_servers[1].'</td><td>'.$x->name_servers[1].'</td></tr></table>';
                    $rtn['ns_header'] = 'Change NS records for '.$x->name.' ('.$x->status.')';
                    $rtn['ns'] = $x;

                    //get DNS records
                  //  $result = dns_get_record($domain, DNS_A + DNS_AAAA + DNS_CNAME + DNS_MX + DNS_TXT);
                  //  $rtn['old_dns'] = $result;

                    //foreach for copy old records to Cloudflare
                  /*  foreach ($result as $key => $v) {
                      if($v['type'] == 'TXT') {
                        if ($dns->addRecord($zoneID, "TXT", $v['host'], $v['txt'],0,false) === true) {
                          $rtn['new_records']['TXT'][] = $v['txt'];
                        }
                      }
                      if($v['type'] == 'MX') {
                        /*if ($dns->addRecord($zoneID, "MX", $v['host'], $v['target'],0,false) === true) {
                            $rtn['new_records']['MX'][] = $v['target'];
                        }*/
                    /*    if ($dns->addRecord($zoneID, "MX", $v['host'], $v['target'],0,false) === true) {
                            $rtn['new_records']['MX'][] = $v['target'];
                        }
                      }
                    }*/

                    if ($dns->addRecord($zoneID, "A", $domain, $this->webbiyip) === true) {
                    //    $rtn['new_records']['A'][] = $this->$webbiyip;
                    }
                    if($dkim === false) {

                    } else {
                      $this->setDKIM($domain,$dkim);
                    }

                  } else {
                      $rtn['error'] = 'Invalid domain';
                  }





      } else { //err
                $rtn['error'] = 'Invalid domain';
      }

      return $rtn;
    }

}
