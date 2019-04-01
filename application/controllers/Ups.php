<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );

class Ups extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public $lang_code = '';
	public $allform=array();
	function __construct() {
		parent::__construct();
		$this->load->library( 'store' );
		$this->load->model( 'product_model' );
		$this->load->model( 'auth_model' );
		$this->load->model( "sitemap_model" );
		$this->load->helper('url');
		$lang_code = $this->uri->segment( 1 );
		$this->lang_code = $this->store->get_lang_code( $lang_code );
		$this->session->set_flashdata( "lang_id", $this->lang_code );
		$this->load->model( "Setting_model" );
		$this->Setting_model->set_currency();
		$this->allform=$this->config->item("form_ups");
	}

	private $allfields = array();

	public function ups() {
		if ( $this->input->get( "view" ) == 'track' ) {
			$this->load->view( 'soap/soap-track' );
		}
		if ( $this->input->get( "view" ) == 'rate' ) {
			$data[ 'fields' ] = array(
				"company_name" => "Company Name",
				"address_one" => "Address",
				"address_two" => "Address (optional)",
				"address_three" => "Address (optional)",
				"city" => "City",
				"country" => "Country(short Code)",
				"state_code" => "State Code (optional)",
				"postal_code" => "Postal Code",
			);
			$data[ 'extra' ] = array(
				"weight" => "Weight",
				"height" => "Height",
				"length" => "Length",
				"width" => "Width",
			);
			$data[ 'shipper' ] = $this->getHtml( $data[ 'fields' ], "shipper" );
			$data[ 'from' ] = $this->getHtml( $data[ 'fields' ], "from" );
			$data[ 'to' ] = $this->getHtml( $data[ 'fields' ], "to" );
			$data[ 'extra' ] = $this->getHtml( $data[ 'extra' ], "dim" );
			$data[ 'valid' ] = $this->allfields;
			$this->load->view( 'soap/soap-rate', $data );
		}
		if ( $this->input->get( "view" ) == 'shipping' ) {
			$this->load->view( 'soap/soap-shipping' );
		}
	}


	public function getHtml( $arr, $join ) {
		$html = '';
		foreach ( $arr as $k => $v ) {

			$kvi = $join . "_" . $k;
			$value=$this->allform[$kvi];
			if ( $k != 'address_two' && $k != 'address_three' )
				array_push( $this->allfields, $kvi );
			$html .= '<div class="form-group">
<label class="col-sm-4 control-label" for="' . $kvi . '">' . $v . '</label>
<div class="col-sm-5">
<input type="text" class="form-control" id="' . $kvi . '" name="' . $kvi . '" value="'.$value.'" placeholder="' . $v . '" />
</div>
</div>';
		}
		return $html;
	}
	public
	function soap() {
		/*
		| -------------------------------------------------------------------
		| Track
		| -------------------------------------------------------------------
		*/
		if ( $this->input->post( "action" ) == "tracking" ) {

		}
		/*
		| -------------------------------------------------------------------
		| UPS Rating API
		| -------------------------------------------------------------------
		*/
		/* Ship To Address */
		if ( $this->input->get( "action" ) == "rate" ) {

			$ups = $this->config->item( "ups" );
			$ups_account = $ups[ "account" ];

			$rate = new Ups\ Rate(
				$ups_account[ 'access' ],
				$ups_account[ 'userid' ],
				$ups_account[ 'passwd' ], false );


			try {
				$shipment = new\ Ups\ Entity\ Shipment();

				$shipperAddress = $shipment->getShipper()->getAddress();
				$post = $this->input->post();
				$shipperAddress->setAttentionName( $post[ 'shipper_company_name' ] );

				$shipperAddress->setAddressLine1( $post[ 'shipper_address_one' ] );
				if ( $post[ 'shipper_address_two' ] != '' )
					$shipperAddress->setAddressLine2( $post[ 'shipper_address_two' ] );
				if ( $post[ 'shipper_address_three' ] != '' )
					$shipperAddress->setAddressLine3( $post[ 'shipper_address_two' ] );
				$shipperAddress->setStateProvinceCode( $post[ 'shipper_state_code' ] );
				$shipperAddress->setCity( $post[ 'shipper_city' ] );
				$shipperAddress->setCountryCode( $post[ 'shipper_country' ] );
				$shipperAddress->setPostalCode( $post[ 'shipper_postal_code' ] );

				$address = new\ Ups\ Entity\ Address();
				$address->setAttentionName( $post[ 'from_company_name' ] );

				$address->setAddressLine1( $post[ 'from_address_one' ] );
				if ( $post[ 'from_address_two' ] != '' )
					$address->setAddressLine2( $post[ 'from_address_two' ] );
				if ( $post[ 'from_address_three' ] != '' )
					$address->setAddressLine3( $post[ 'from_address_three' ] );
				$address->setStateProvinceCode( $post[ 'from_state_code' ] );
				$address->setCity( $post[ 'from_city' ] );
				$address->setCountryCode( $post[ 'from_country' ] );
				$address->setPostalCode( $post[ 'from_postal_code' ] );
				$shipFrom = new\ Ups\ Entity\ ShipFrom();
				$shipFrom->setAddress( $address );

				$shipment->setShipFrom( $shipFrom );

				$shipTo = $shipment->getShipTo();

				$shipTo->setAttentionName( $post[ 'to_company_name' ] );
				$shipToAddress = $shipTo->getAddress();
				$shipToAddress->setAddressLine1( $post[ 'to_address_one' ] );
				if ( $post[ 'to_address_two' ] != '' )
					$shipToAddress->setAddressLine2( $post[ 'to_address_two' ] );
				if ( $post[ 'to_address_three' ] != '' )
					$shipToAddress->setAddressLine3( $post[ 'to_address_three' ] );
				$shipToAddress->setStateProvinceCode( $post[ 'to_state_code' ] );
				$shipToAddress->setCity( $post[ 'to_city' ] );
				$shipToAddress->setCountryCode( $post[ 'to_country' ] );
				$shipToAddress->setPostalCode( $post[ 'to_postal_code' ] );

				$package = new\ Ups\ Entity\ Package();
				$package->getPackagingType()->setCode( \Ups\ Entity\ PackagingType::PT_TUBE );
				$package->getPackageWeight()->setWeight( $post[ 'dim_weight' ] );

				// if you need this (depends of the shipper country)
				$weightUnit = new\ Ups\ Entity\ UnitOfMeasurement;
				$weightUnit->setCode( \Ups\ Entity\ UnitOfMeasurement::UOM_KGS );
				$package->getPackageWeight()->setUnitOfMeasurement( $weightUnit );

				$dimensions = new\ Ups\ Entity\ Dimensions();
				/*$dimensions->setHeight(40);
				$dimensions->setWidth(30);
				$dimensions->setLength(35);*/
				$dimensions->setHeight( $post[ 'dim_height' ] );
				$dimensions->setWidth( $post[ 'dim_width' ] );
				$dimensions->setLength( $post[ 'dim_length' ] );

				$unit = new\ Ups\ Entity\ UnitOfMeasurement;
				$unit->setCode( \Ups\ Entity\ UnitOfMeasurement::UOM_CM );

				$dimensions->setUnitOfMeasurement( $unit );
				$package->setDimensions( $dimensions );

				$shipment->addPackage( $package );

				$res = $rate->getRate( $shipment );

				echo json_encode( $res );
			} catch ( Exception $e ) {
				$this->output->set_status_header( 422 );
				$msg = ( array )$e;

				echo( json_encode( $msg ) );


			}
		}

		/*
		| -------------------------------------------------------------------
		| UPS Shipping API
		| -------------------------------------------------------------------
		*/
		if ( $this->input->post( "action" ) == "shipping" ) {

		}

	}


}