<?php
declare(strict_types=1);

class GardenaCloud extends IPSModule
{
    private $oauthIdentifer = 'husqvarna';

    // GARDENA smart system API
    private const SMART_SYSTEM_BASE_URL = 'https://api.smart.gardena.dev/v1';
    private const AUTOMOVER_CONNECT_SYSTEM_BASE_URL = 'https://api.amc.husqvarna.dev/v1';
    private const LOCATIONS = '/locations';
    private const WEBSOCKET ='/websocket';
    private const APIKEY = 'b42b22bf-5482-4f0b-b78a-9c5558ff5b4a';
    // mover mode
    private const MAIN_AREA = 'MAIN_AREA'; // Mower will mow until low battery. Go home and charge. Leave and continue mowing. Week schedule is used. Schedule can be overridden with forced park or forced mowing.
    private const DEMO = 'DEMO'; // Same as main area, but shorter times. No blade operation.
    private const SECONDARY_AREA = 'SECONDARY_AREA'; // Mower is in secondary area. Schedule is overridden with forced park or forced mowing. Mower will mow for request time or untill the battery runs out.
    private const HOME = 'HOME'; // Mower goes home and parks forever. Week schedule is not used. Cannot be overridden with forced mowing.
    private const UNKNOWN = 'UNKNOWN'; // Unknown mode
    // mover activity
    private const ACIVITY_UNKNOWN = 'UNKNOWN'; // Unknown activity.
    private const NOT_APPLICABLE = 'NOT_APPLICABLE'; // Manual start required in mower.
    private const MOWING = 'MOWING'; // Mower is mowing lawn. If in demo mode the blades are not in operation.
    private const GOING_HOME = 'GOING_HOME'; // Mower is going home to the charging station.
    private const CHARGING = 'CHARGING'; // Mower is charging in station due to low battery.
    private const LEAVING = 'LEAVING'; // Mower is leaving the charging station.
    private const PARKED_IN_CS = 'PARKED_IN_CS'; // Mower is parked in charging station.
    private const STOPPED_IN_GARDEN = 'STOPPED_IN_GARDEN'; // Mower has stopped. Needs manual action to resume.
    // mover state
    private const STATE_UNKNOWN = 'UNKNOWN'; // Unknown state.
    private const STATE_NOT_APPLICABLE = 'NOT_APPLICABLE';
    private const STATE_PAUSED = 'PAUSED'; // Mower has been paused by user.
    private const STATE_IN_OPERATION = 'IN_OPERATION'; // See value in activity for status.
    private const STATE_WAIT_UPDATING = 'WAIT_UPDATING'; // Mower is downloading new firmware.
    private const STATE_WAIT_POWER_UP = 'WAIT_POWER_UP'; // Mower is performing power up tests.
    private const STATE_RESTRICTED = 'RESTRICTED'; // Mower can currently not mow due to week calender, or override park.
    private const STATE_OFF = 'OFF'; // Mower is turned off.
    private const STATE_STOPPED = 'STOPPED'; // Mower is stopped requires manual action.
    private const STATE_ERROR = 'ERROR'; // An error has occurred. Check errorCode. Mower requires manual action.
    private const STATE_FATAL_ERROR = 'FATAL_ERROR'; // An error has occurred. Check errorCode. Mower requires manual action.
    private const STATE_ERROR_AT_POWER_UP = 'ERROR_AT_POWER_UP'; // An error has occurred. Check errorCode. Mower requires manual action.


    // error codes mover
    /*
     * 0    Unexpected error
1    Outside working area
2    No loop signal
3    Wrong loop signal
4    Loop sensor problem, front
5    Loop sensor problem, rear
6    Loop sensor problem, left
7    Loop sensor problem, right
8    Wrong PIN code
9    Trapped
10    Upside down
11    Low battery
12    Empty battery
13    No drive
14    Mower lifted
15    Lifted
16    Stuck in charging station
17    Charging station blocked
18    Collision sensor problem, rear
19    Collision sensor problem, front
20    Wheel motor blocked, right
21    Wheel motor blocked, left
22    Wheel drive problem, right
23    Wheel drive problem, left
24    Cutting system blocked
25    Cutting system blocked
26    Invalid sub-device combination
27    Settings restored
28    Memory circuit problem
29    Slope too steep
30    Charging system problem
31    STOP button problem
32    Tilt sensor problem
33    Mower tilted
34    Cutting stopped - slope too steep
35    Wheel motor overloaded, right
36    Wheel motor overloaded, left
37    Charging current too high
38    Electronic problem
39    Cutting motor problem
40    Limited cutting height range
41    Unexpected cutting height adj
42    Limited cutting height range
43    Cutting height problem, drive
44    Cutting height problem, curr
45    Cutting height problem, dir
46    Cutting height blocked
47    Cutting height problem
48    No response from charger
49    Ultrasonic problem
50    Guide 1 not found
51    Guide 2 not found
52    Guide 3 not found
53    GPS navigation problem
54    Weak GPS signal
55    Difficult finding home
56    Guide calibration accomplished
57    Guide calibration failed
58    Temporary battery problem
59    Temporary battery problem
60    Temporary battery problem
61    Temporary battery problem
62    Temporary battery problem
63    Temporary battery problem
64    Temporary battery problem
65    Temporary battery problem
66    Battery problem
67    Battery problem
68    Temporary battery problem
69    Alarm! Mower switched off
70    Alarm! Mower stopped
71    Alarm! Mower lifted
72    Alarm! Mower tilted
73    Alarm! Mower in motion
74    Alarm! Outside geofence
75    Connection changed
76    Connection NOT changed
77    Com board not available
78    Slipped - Mower has Slipped.Situation not solved with moving pattern
79    Invalid battery combination - Invalid combination of different battery types.
80    Cutting system imbalance    Warning
81    Safety function faulty
82    Wheel motor blocked, rear right
83    Wheel motor blocked, rear left
84    Wheel drive problem, rear right
85    Wheel drive problem, rear left
86    Wheel motor overloaded, rear right
87    Wheel motor overloaded, rear left
88    Angular sensor problem
89    Invalid system configuration
90    No power in charging station
     */
    public function Create()
    {
        //Never delete this line!
        parent::Create();

        $this->RegisterPropertyInteger("UpdateInterval", 15);
        $this->RegisterTimer("Update", 0, "GARDENA_Update(" . $this->InstanceID . ");");
        $this->RegisterAttributeString('Token', '');
        $this->RegisterAttributeString('location_id', '');
        $this->RegisterAttributeString('location_name', '');
        $this->RegisterAttributeString('snapshot', '[]');
        $this->RegisterPropertyInteger("ImportCategoryID", 0);

        //we will wait until the kernel is ready
        $this->RegisterMessage(0, IPS_KERNELMESSAGE);
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        if (IPS_GetKernelRunlevel() !== KR_READY) {
            return;
        }

        $this->RegisterOAuth($this->oauthIdentifer);
        $gardena_interval = $this->ReadPropertyInteger('UpdateInterval');
        $this->SetGardenaInterval($gardena_interval);

        if ($this->ReadAttributeString('Token') == '') {
            $this->SetStatus(IS_INACTIVE);
        } else {
            $this->SetStatus(IS_ACTIVE);
        }
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        switch ($Message) {
            case IM_CHANGESTATUS:
                if ($Data[0] === IS_ACTIVE) {
                    $this->ApplyChanges();
                }
                break;

            case IPS_KERNELMESSAGE:
                if ($Data[0] === KR_READY) {
                    $this->ApplyChanges();
                }
                break;

            default:
                break;
        }
    }

    public function Update()
    {
        $this->UpdateStatus();
    }

    public function UpdateStatus()
    {
        $snapshot = $this->RequestSnapshot();

        $this->SendDebug('Send Snapshot', $snapshot, 0);
        $this->SendDataToChildren(json_encode(Array("DataID" => "{E95D48A0-6A3D-3F4E-B73E-7645BBFC6A06}", "Buffer" => $snapshot)));
        return $snapshot;
    }

    private function SetGardenaInterval($gardena_interval): void
    {
        if($gardena_interval < 15 && $gardena_interval != 0)
        {
            $gardena_interval = 15;
        }
        $interval     = $gardena_interval * 1000  * 60; // minutes
        $this->SetTimerInterval('Update', $interval);
    }

    public function CheckToken()
    {
        $token = $this->ReadAttributeString('Token');
        return $token;
    }

    public function GetToken()
    {
        $token = $this->FetchAccessToken();
        return $token;
    }

    private function RegisterOAuth($WebOAuth)
    {
        $ids = IPS_GetInstanceListByModuleID('{F99BF07D-CECA-438B-A497-E4B55F139D37}');
        if (count($ids) > 0) {
            $clientIDs = json_decode(IPS_GetProperty($ids[0], 'ClientIDs'), true);
            $found = false;
            foreach ($clientIDs as $index => $clientID) {
                if ($clientID['ClientID'] == $WebOAuth) {
                    if ($clientID['TargetID'] == $this->InstanceID) {
                        return;
                    }
                    $clientIDs[$index]['TargetID'] = $this->InstanceID;
                    $found = true;
                }
            }
            if (!$found) {
                $clientIDs[] = ['ClientID' => $WebOAuth, 'TargetID' => $this->InstanceID];
            }
            IPS_SetProperty($ids[0], 'ClientIDs', json_encode($clientIDs));
            IPS_ApplyChanges($ids[0]);
        }
    }

    /**
     * This function will be called by the register button on the property page!
     */
    public function Register()
    {

        //Return everything which will open the browser
        return 'https://oauth.ipmagic.de/authorize/' . $this->oauthIdentifer . '?username=' . urlencode(IPS_GetLicensee());
    }

    /** Exchange our Authentication Code for a permanent Refresh Token and a temporary Access Token
     * @param $code
     *
     * @return mixed
     */
    private function FetchRefreshToken($code)
    {
        $this->SendDebug('FetchRefreshToken', 'Use Authentication Code to get our precious Refresh Token!', 0);
        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query(['code' => $code])]];
        $context = stream_context_create($options);
        $result = file_get_contents('https://oauth.ipmagic.de/access_token/' . $this->oauthIdentifer, false, $context);

        $data = json_decode($result);
        $this->SendDebug('Symcon Connect Data', $result, 0);
        if (!isset($data->token_type) || $data->token_type != 'Bearer') {
            die('Bearer Token expected');
        }

        //Save temporary access token
        $this->FetchAccessToken($data->access_token, time() + $data->expires_in);

        //Return RefreshToken
        return $data->refresh_token;
    }

    /**
     * This function will be called by the OAuth control. Visibility should be protected!
     */
    protected function ProcessOAuthData()
    {

        // <REDIRECT_URI>?code=<AUTHORIZATION_CODE>&state=<STATE>
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if (!isset($_GET['code'])) {
                die('Authorization Code expected');
            }

            $token = $this->FetchRefreshToken($_GET['code']);

            $this->SendDebug('ProcessOAuthData', "OK! Let's save the Refresh Token permanently", 0);

            $this->WriteAttributeString('Token', $token);

            //This will enforce a reload of the property page. change this in the future, when we have more dynamic forms
            IPS_ApplyChanges($this->InstanceID);
        } else {

            //Just print raw post data!
            $payload = file_get_contents('php://input');
            $this->SendDebug('OAuth Response', $payload, 0);
        }
    }

    private function FetchAccessToken($Token = '', $Expires = 0)
    {

        //Exchange our Refresh Token for a temporary Access Token
        if ($Token == '' && $Expires == 0) {

            //Check if we already have a valid Token in cache
            $data = $this->GetBuffer('AccessToken');
            if ($data != '') {
                $data = json_decode($data);
                if (time() < $data->Expires) {
                    $this->SendDebug('FetchAccessToken', 'OK! Access Token is valid until ' . date('d.m.y H:i:s', $data->Expires), 0);
                    return $data->Token;
                }
            }

            $this->SendDebug('FetchAccessToken', 'Use Refresh Token to get new Access Token!', 0);
            $options = [
                'http' => [
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query(['refresh_token' => $this->ReadAttributeString('Token')])]];
            $context = stream_context_create($options);
            $result = file_get_contents('https://oauth.ipmagic.de/access_token/' . $this->oauthIdentifer, false, $context);

            $data = json_decode($result);
            $this->SendDebug('Symcon Connect Data', $result, 0);
            if (!isset($data->token_type) || $data->token_type != 'Bearer') {
                die('Bearer Token expected');
            }

            //Update parameters to properly cache it in the next step
            $Token = $data->access_token;
            $Expires = time() + $data->expires_in;

            //Update Refresh Token if we received one! (This is optional)
            if (isset($data->refresh_token)) {
                $this->SendDebug('FetchAccessToken', "NEW! Let's save the updated Refresh Token permanently", 0);

                $this->WriteAttributeString('Token', $data->refresh_token);
            }
        }

        $this->SendDebug('FetchAccessToken', 'CACHE! New Access Token is valid until ' . date('d.m.y H:i:s', $Expires), 0);

        //Save current Token
        $this->SetBuffer('AccessToken', json_encode(['Token' => $Token, 'Expires' => $Expires]));

        //Return current Token
        return $Token;
    }

    private function FetchData($url)
    {

        $this->SendDebug("AT", $this->FetchAccessToken(), 0);

        $opts = array(
            "http" => array(
                "method" => "GET",
                "header" => "Authorization: Bearer " . $this->FetchAccessToken() . "\r\nAuthorization-Provider: husqvarna\r\nX-Api-Key: " . self::APIKEY . "\r\n",
                "ignore_errors" => true
            )
        );
        $context = stream_context_create($opts);

        $result = file_get_contents($url, false, $context);
        $http_error = $http_response_header[0];
        $result = $this->GetErrorMessage($http_error, $result);
        return $result;
    }

    private function GetErrorMessage($http_error, $result)
    {
        $response =  $result;
        if ((strpos($http_error, '200') > 0)) {
            $this->SendDebug('HTTP Response Header',  'Success. Response Body: ' . $result, 0);
        }
        elseif((strpos($http_error, '401') > 0)) {
            $this->SendDebug('HTTP Response Header', 'Failure, user could not be authenticated. Authorization-Provider or X-Api-Key header or Beaerer Token missing or invalid. Response Body: ' . $result, 0);
            $response =  false;
        }
        elseif((strpos($http_error, '404') > 0)) {
            $this->SendDebug('HTTP Response Header', 'Failure, location not found. Response Body: ' . $result, 0);
            $response =  false;
        }
        elseif((strpos($http_error, '500') > 0)) {
            $this->SendDebug('HTTP Response Header', 'Failure, internal error. Response Body: ' . $result, 0);
            $response =  false;
        }
        elseif((strpos($http_error, '502') > 0)) {
            $this->SendDebug('HTTP Response Header', 'Failure, backend error. Response Body: ' . $result, 0);
            $response =  false;
        }
        else{
            $this->SendDebug('HTTP Response Header', $http_error . 'Response Body: ' . $result, 0);
            $response =  false;
        }

        if($result == '{"message":"Limit Exceeded"}')
        {
            $this->SendDebug('Gardena API', 'Limit Exceeded', 0);
        }
        return $response;
    }


    // GARDENA smart system API


    /** Announce your desire to receive realtime events.
     * @return string
     */
    public function GetWebSocket()
    {
        $locationId = $this->ReadAttributeString('location_id');
        $this->SendDebug('Gardena Location ID', $locationId, 0);
        $websocket_response = '';
        if($locationId != '')
        {
            $service_id = 'request-12312'; // todo
            $payload = ['data' => [
                'id'=> $service_id,
                'type'=> 'WEBSOCKET',
                'attributes'=> [
                    'locationId'=> $locationId
                ]
            ]];
            $data = json_encode($payload);
            $websocket_response = $this->PostData(self::SMART_SYSTEM_BASE_URL . self::WEBSOCKET, $data);
        }
        return $websocket_response;
    }

    // Snapshot
    //Fetch current state of devices. Rate limited, so frequent polling not possible.

    /** Get location with its devices and services (a device can have multiple services).
     * @return bool|false|string
     */
    public function RequestSnapshot()
    {
        $location_id = $this->ReadAttributeString('location_id');
        if ($location_id != '') {
            $snapshot = $this->FetchData(self::SMART_SYSTEM_BASE_URL . self::LOCATIONS . "/" . $location_id);
        } else {
            $snapshot = '[]';
        }
        if($snapshot  === false)
        {
            $this->SendDebug('Gardena Location Snapshot', 'Could not get snapshot', 0);
            $snapshot = '[]';
        }
        else
        {
            $this->SendDebug('Gardena Location Snapshot', $snapshot, 0);
            $this->WriteAttributeString('snapshot', $snapshot);
        }
        return $snapshot;
    }

    public function RequestSnapshotBuffer()
    {
        // $this->WriteAttributeString('snapshot', '[]');
        $snapshot = $this->ReadAttributeString('snapshot');
        $this->SendDebug('Gardena Location Snapshot Buffer', $snapshot, 0);
        if($snapshot == '[]')
        {
            $snapshot = $this->RequestSnapshot();
            $this->SendDebug('Gardena Request Snapshot', $snapshot, 0);
        }
        return $snapshot;
    }

    /** List Locations
     * @return bool|false|string
     */
    public function RequestLocations()
    {
        $location_id = false;
        $state_location = $this->FetchData(self::SMART_SYSTEM_BASE_URL . self::LOCATIONS);
        if($state_location === false)
        {
            $this->SendDebug('Gardena Locations', 'Could not get location', 0);
        }
        else
        {
            $this->SendDebug('Gardena Locations', strval($state_location), 0);
            $location_data = json_decode($state_location, true);
            $location_id = $location_data['data'][0]['id'];
            $location_name = $location_data['data'][0]['attributes']['name'];
            $this->SendDebug('Gardena Location Name', $location_name, 0);
            $this->WriteAttributeString('location_name', $location_name);
            $this->SendDebug('Gardena Location ID', $location_id, 0);
            $this->WriteAttributeString('location_id', $location_id);
        }
        return $location_id;
    }

    /** Get Gardena Configuration
     * @return bool|false|string
     */
    public function GetConfiguration()
    {
        $snapshot = '[]';
        $location_id = $this->ReadAttributeString('location_id');
        if ($location_id != '') {
            $snapshot = $this->RequestSnapshot();
        } else {
            $locations = $this->RequestLocations();
            if(!$locations === false)
            {
                $snapshot = $this->RequestSnapshot();
            }
        }
        return $snapshot;
    }

    private function PutData($url, $content)
    {
        $this->SendDebug("AT", $this->FetchAccessToken(), 0);

        $opts = array(
            "http" => array(
                "method" => "PUT",
                "header" => "Authorization: Bearer " . $this->FetchAccessToken() . "\r\nAuthorization-Provider: husqvarna\r\nX-Api-Key: " . self::APIKEY . "\r\n" . 'Content-Type: application/json' . "\r\n"
                    . 'Content-Length: ' . strlen($content) . "\r\n",
                'content' => $content,
                "ignore_errors" => true
            )
        );
        $context = stream_context_create($opts);

        $result = file_get_contents($url, false, $context);

        if ((strpos($http_response_header[0], '200') === false)) {
            $this->SendDebug('HTTP Response Header', $http_response_header[0] . 'Response Body: ' . $result, 0);
            $this->GetErrorMessage($result);
            return false;
        }
        return $result;
    }

    private function PostData($url, $content)
    {

        $this->SendDebug("AT", $this->FetchAccessToken(), 0);

        $opts = array(
            "http" => array(
                "method" => "POST",
                "header" => "Authorization: Bearer " . $this->FetchAccessToken() . "\r\nAuthorization-Provider: husqvarna\r\nX-Api-Key: " . self::APIKEY . "\r\n" . 'Content-Type: application/json' . "\r\n"
                    . 'Content-Length: ' . strlen($content) . "\r\n",
                'content' => $content,
                "ignore_errors" => true
            )
        );
        $context = stream_context_create($opts);

        $result = file_get_contents($url, false, $context);

        if ((strpos($http_response_header[0], '200') === false)) {
            $this->SendDebug('HTTP Response Header', $http_response_header[0] . 'Response Body: ' . $result, 0);
            $this->GetErrorMessage($result);
            return false;
        }
        return $result;
    }

    public function ForwardData($data)
    {
        $data = json_decode($data);

        if (strlen($data->Payload) > 0) {
            $type = $data->Type;
            if($type == 'PUT')
            {
                $this->SendDebug('ForwardData', $data->Endpoint . ', Payload: ' . $data->Payload, 0);
                $response = $this->PutData(self::SMART_SYSTEM_BASE_URL . $data->Endpoint, $data->Payload);
            }
            elseif($type == 'POST')
            {
                $this->SendDebug('ForwardData', $data->Endpoint . ', Payload: ' . $data->Payload, 0);
                $response = $this->PostData(self::SMART_SYSTEM_BASE_URL . $data->Endpoint, $data->Payload);
            }
        } else {
            $this->SendDebug('ForwardData', $data->Endpoint, 0);
            if($data->Endpoint == 'location_id')
            {
                $response = $this->ReadAttributeString('location_id');
            }
            elseif($data->Endpoint == 'snapshot')
            {
                $response = $this->RequestSnapshot();
            }
            elseif($data->Endpoint == 'snapshotbuffer')
            {
                $response = $this->RequestSnapshotBuffer();
            }
            elseif($data->Endpoint == 'request_location_id')
            {
                $response = $this->RequestLocations();
            }
            elseif($data->Endpoint == 'token')
            {
                $response = $this->CheckToken();
            }
        }
        return $response;
    }

    /** Get Device Type
     * @param $device
     * @return array
     */
    private function GetDeviceType($device)
    {
        $model_type = $device['attributes']['modelType']['value'];
        if ($model_type == 'GARDENA smart Irrigation Control') {
            $data = $this->GetIrrigationControlData($device);
        } elseif ($model_type == 'GARDENA smart Sensor') {
            $data = $this->GetSensorInfo($device);
        }
        return $data;
    }

    /** Get Sensor Info
     * @param $device
     * @return array
     */
    private function GetSensorInfo($device)
    {
        $id = $device['id'];
        $name = $device['attributes']['name']['value'];
        $battery_level = $device['attributes']['batteryLevel']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'battery level: ' . $battery_level . '%', 0);
        $battery_level_timestamp = $device['attributes']['batteryLevel']['timestamp'];
        $this->SendDebug('Gardena Device ' . $name, 'battery level timestamp: ' . $battery_level_timestamp, 0);
        $battery_state = $device['attributes']['batteryState']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'battery state: ' . $battery_state, 0);
        $battery_state_timestamp = $device['attributes']['batteryState']['timestamp'];
        $this->SendDebug('Gardena Device ' . $name, 'battery state timestamp: ' . $battery_state_timestamp, 0);
        $rf_link_level = $device['attributes']['rfLinkLevel']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'RF link level: ' . $rf_link_level . '%', 0);
        $rf_link_level_timestamp = $device['attributes']['rfLinkLevel']['timestamp'];
        $this->SendDebug('Gardena Device ' . $name, 'RF link level timestamp: ' . $rf_link_level_timestamp, 0);
        $serial = $device['attributes']['serial']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'serial: ' . $serial, 0);
        $rf_link_state = $device['attributes']['rfLinkState']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'RF link state: ' . $rf_link_state, 0);

        return ['id' => $id, 'name' => $name, 'serial' => $serial, 'rf_link_state' => $rf_link_state];
    }

    /** Get Irrigation Control Data
     * @param $device
     * @return array
     */
    private function GetIrrigationControlData($device)
    {
        $id = $device['id'];
        $name = $device['attributes']['name']['value'];
        $serial = $device['attributes']['serial']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'serial: ' . $serial, 0);
        $rf_link_state = $device['attributes']['rfLinkState']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'RF link state: ' . $rf_link_state, 0);
        return ['id' => $id, 'name' => $name, 'serial' => $serial, 'rf_link_state' => $rf_link_state];
    }

    /***********************************************************
     * Configuration Form
     ***********************************************************/

    /**
     * build configuration form
     * @return string
     */
    public function GetConfigurationForm()
    {
        // return current form
        $Form = json_encode([
            'elements' => $this->FormHead(),
            'actions' => $this->FormActions(),
            'status' => $this->FormStatus()
        ]);
        $this->SendDebug('FORM', $Form, 0);
        $this->SendDebug('FORM', json_last_error_msg(), 0);
        return $Form;
    }

    /**
     * return form configurations on configuration step
     * @return array
     */
    protected function FormHead()
    {
        $visibility_register = false;
        //Check Gardena connection
        if ($this->ReadAttributeString('Token') == '') {
            $visibility_register = true;
        }

        $form = [
            [
                'type' => 'Label',
                'visible' => $visibility_register,
                'caption' => 'Gardena: Please register with your Gardena account!'
            ],
            [
                'type' => 'Button',
                'visible' => true,
                'caption' => 'Register',
                'onClick' => 'echo GARDENA_Register($id);'
            ],
            [
                'type' => 'Label',
                'visible' => true,
                'label' => 'Update interval in minutes (minimum 15 minutes):'
            ],
            [
                'name' => 'UpdateInterval',
                'visible' => true,
                'type' => 'IntervalBox',
                'caption' => 'minutes'
            ]
        ];
        return $form;
    }

    /**
     * return form actions by token
     * @return array
     */
    protected function FormActions()
    {
        //Check Connect availability
        $ids = IPS_GetInstanceListByModuleID('{9486D575-BE8C-4ED8-B5B5-20930E26DE6F}');
        if (IPS_GetInstance($ids[0])['InstanceStatus'] != IS_ACTIVE) {
            $visibility_label1 = true;
            $visibility_label2 = false;
        } else {
            $visibility_label1 = false;
            $visibility_label2 = true;
        }
        $location_id = $this->ReadAttributeString('location_id');
        $location_name = $this->ReadAttributeString('location_name');
        if ($location_id != '') {
            $visibility_config = true;
        } else {
            $visibility_config = false;
        }
       $form = [
            [
                'type' => 'Label',
                'visible' => $visibility_label1,
                'caption' => 'Error: Symcon Connect is not active!'
            ],
            [
                'type' => 'Label',
                'visible' => $visibility_label2,
                'caption' => 'Status: Symcon Connect is OK!'
            ],
           [
               'type' => 'Label',
               'visible' => $visibility_config,
               'caption' => $this->Translate('Gardena Location: ') . $location_name
           ],
            [
                'type' => 'Label',
                'visible' => true,
                'caption' => 'Read Gardena configuration:'
            ],
            [
                'type' => 'Button',
                'visible' => true,
                'caption' => 'Read configuration',
                'onClick' => 'GARDENA_GetConfiguration($id);'
            ]
        ];
        return $form;
    }

    /**
     * return from status
     * @return array
     */
    protected function FormStatus()
    {
        $form = [
            [
                'code' => IS_CREATING,
                'icon' => 'inactive',
                'caption' => 'Creating instance.'
            ],
            [
                'code' => IS_ACTIVE,
                'icon' => 'active',
                'caption' => 'configuration valid.'
            ],
            [
                'code' => IS_INACTIVE,
                'icon' => 'inactive',
                'caption' => 'interface closed.'
            ],
            [
                'code' => 201,
                'icon' => 'inactive',
                'caption' => 'Please follow the instructions.'
            ],
            [
                'code' => 202,
                'icon' => 'error',
                'caption' => 'no category selected.'
            ]
        ];

        return $form;
    }
}