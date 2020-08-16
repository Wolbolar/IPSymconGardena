<?php
declare(strict_types=1);

class GardenaCloud extends IPSModule
{
    private const SMART_SYSTEM_BASE_URL = 'https://oauth.symcon.cloud/proxy/gardena/v1';

    // GARDENA smart system API
    private const AUTOMOVER_CONNECT_SYSTEM_BASE_URL = 'https://api.amc.husqvarna.dev/v1';
    private const LOCATIONS = '/locations';
    private const WEBSOCKET = '/websocket';
    private const MAIN_AREA = 'MAIN_AREA';
    // mover mode
    private const DEMO = 'DEMO'; // Mower will mow until low battery. Go home and charge. Leave and continue mowing. Week schedule is used. Schedule can be overridden with forced park or forced mowing.
    private const SECONDARY_AREA = 'SECONDARY_AREA'; // Same as main area, but shorter times. No blade operation.
    private const HOME = 'HOME'; // Mower is in secondary area. Schedule is overridden with forced park or forced mowing. Mower will mow for request time or untill the battery runs out.
    private const UNKNOWN = 'UNKNOWN'; // Mower goes home and parks forever. Week schedule is not used. Cannot be overridden with forced mowing.
    private const ACIVITY_UNKNOWN = 'UNKNOWN'; // Unknown mode
    // mover activity
    private const NOT_APPLICABLE = 'NOT_APPLICABLE'; // Unknown activity.
    private const MOWING = 'MOWING'; // Manual start required in mower.
    private const GOING_HOME = 'GOING_HOME'; // Mower is mowing lawn. If in demo mode the blades are not in operation.
    private const CHARGING = 'CHARGING'; // Mower is going home to the charging station.
    private const LEAVING = 'LEAVING'; // Mower is charging in station due to low battery.
    private const PARKED_IN_CS = 'PARKED_IN_CS'; // Mower is leaving the charging station.
    private const STOPPED_IN_GARDEN = 'STOPPED_IN_GARDEN'; // Mower is parked in charging station.
    private const STATE_UNKNOWN = 'UNKNOWN'; // Mower has stopped. Needs manual action to resume.
    // mover state
    private const STATE_NOT_APPLICABLE = 'NOT_APPLICABLE'; // Unknown state.
    private const STATE_PAUSED = 'PAUSED';
    private const STATE_IN_OPERATION = 'IN_OPERATION'; // Mower has been paused by user.
    private const STATE_WAIT_UPDATING = 'WAIT_UPDATING'; // See value in activity for status.
    private const STATE_WAIT_POWER_UP = 'WAIT_POWER_UP'; // Mower is downloading new firmware.
    private const STATE_RESTRICTED = 'RESTRICTED'; // Mower is performing power up tests.
    private const STATE_OFF = 'OFF'; // Mower can currently not mow due to week calender, or override park.
    private const STATE_STOPPED = 'STOPPED'; // Mower is turned off.
    private const STATE_ERROR = 'ERROR'; // Mower is stopped requires manual action.
    private const STATE_FATAL_ERROR = 'FATAL_ERROR'; // An error has occurred. Check errorCode. Mower requires manual action.
    private const STATE_ERROR_AT_POWER_UP = 'ERROR_AT_POWER_UP'; // An error has occurred. Check errorCode. Mower requires manual action.

    private const PICTURE_LOGO_GARDENA = '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';
    private $oauthIdentifer = 'gardena';

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        $this->RegisterPropertyString("user", '');
        $this->RegisterPropertyString("password", '');
        $this->RegisterPropertyInteger("UpdateInterval", 15);
        $this->RegisterPropertyInteger("WebsocketUpdateInterval", 60);
        $this->RegisterTimer("Update", 0, "GARDENA_Update(" . $this->InstanceID . ");");
        $this->RegisterTimer("UpdateWebsocket", 0, "GARDENA_UpdateWebsocket(" . $this->InstanceID . ");");
        $this->RegisterAttributeString('Token', '');
        $this->RegisterAttributeString('location_id', '');
        $this->RegisterAttributeString('location_name', '');
        $this->RegisterAttributeString('snapshot', '[]');
        $this->RegisterPropertyInteger("ImportCategoryID", 0);
        $this->RegisterAttributeString('websocket_url', '');
        $this->RegisterAttributeBoolean('alternative_url', false);
        $this->RegisterAttributeBoolean('limit', false);
        $this->RegisterAttributeString('user_id', '');
        $this->RegisterAttributeString('locations', '');
        $this->RegisterAttributeString('devices', '[]');
        $this->RegisterAttributeBoolean('extended_debug', false);

        $this->RequireParent("{D68FD31F-0E90-7019-F16C-1949BD3079EF}"); // Websocket I/O

        //we will wait until the kernel is ready
        $this->RegisterMessage(0, IPS_KERNELMESSAGE);
    }

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

    /** @noinspection PhpMissingParentCallCommonInspection */

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
        $gardena_websocket_interval = $this->ReadPropertyInteger('WebsocketUpdateInterval');
        $this->SetGardenaWebsocketInterval($gardena_websocket_interval);

        if ($this->ReadAttributeString('Token') == '') {
            $this->SetStatus(IS_INACTIVE);
        } else {
            $this->SetStatus(IS_ACTIVE);
        }
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

    public function ExtendedDebug(bool $state)
    {
        $this->WriteAttributeBoolean('extended_debug', $state);
        $debug = $this->ReadAttributeBoolean('extended_debug');
        return $debug;
    }

    private function SetGardenaInterval($gardena_interval): void
    {
        if ($gardena_interval < 15 && $gardena_interval != 0) {
            $gardena_interval = 15;
        }
        $interval = $gardena_interval * 1000 * 60; // minutes
        $this->SetTimerInterval('Update', $interval);
    }

    private function SetGardenaWebsocketInterval($gardena_websocket_interval): void
    {
        $interval = $gardena_websocket_interval * 1000 ; // seconds
        $this->SetTimerInterval('UpdateWebsocket', $interval);
    }

    public function Update()
    {
        $snapshot = $this->RequestSnapshot();

        $this->SendDebug('Send Snapshot', $snapshot, 0);
        $this->SendDataToChildren(json_encode(array("DataID" => "{E95D48A0-6A3D-3F4E-B73E-7645BBFC6A06}", "Buffer" => $snapshot)));
        return $snapshot;
    }

    public function UpdateWebsocket()
    {
        $websocket_response = $this->GetWebSocket();
        $this->SendDebug('Refresh Websocket', json_encode($websocket_response), 0);
        return $websocket_response;
    }

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
        if ($snapshot === false) {
            $this->SendDebug('Gardena Location Snapshot', 'Could not get snapshot', 0);
            $snapshot = '[]';
        } else {
            $this->SendDebug('Gardena Location Snapshot', $snapshot, 0);
            $this->WriteAttributeString('snapshot', $snapshot);
        }
        return $snapshot;
    }

    private function FetchData($url)
    {

        $this->SendDebug("AT", $this->FetchAccessToken(), 0);

        $opts = array(
            "http" => array(
                "method" => "GET",
                "header" => "Authorization: Bearer " . $this->FetchAccessToken() . "\r\n",
                "ignore_errors" => true
            )
        );
        $context = stream_context_create($opts);

        $result = file_get_contents($url, false, $context);
        $http_error = $http_response_header[0];
        $result = $this->GetErrorMessage($http_error, $result);
        return $result;
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
            $result = @file_get_contents('https://oauth.ipmagic.de/access_token/' . $this->oauthIdentifer, false, $context);
            if($result == false)
            {
                $this->SendDebug('FetchAccessToken', "failed to open stream: HTTP request failed! HTTP/1.1 400 Bad Request", 0);
            }
            else{
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
        }

        $this->SendDebug('FetchAccessToken', 'CACHE! New Access Token is valid until ' . date('d.m.y H:i:s', $Expires), 0);

        //Save current Token
        $this->SetBuffer('AccessToken', json_encode(['Token' => $Token, 'Expires' => $Expires]));

        //Return current Token
        return $Token;
    }

    private function GetErrorMessage($http_error, $result)
    {
        $response = $result;
        if ((strpos($http_error, '200') > 0)) {
            $this->SendDebug('HTTP Response Header', 'Success. Response Body: ' . $result, 0);
        } elseif ((strpos($http_error, '201') > 0)) {
            $this->SendDebug('HTTP Response Header', 'Success. CreatedResponse Body: ' . $result, 0);
        } elseif ((strpos($http_error, '401') > 0)) {
            $this->SendDebug('HTTP Response Header', 'Failure, user could not be authenticated. Authorization-Provider or X-Api-Key header or Beaerer Token missing or invalid. Response Body: ' . $result, 0);
            $response = false;
        } elseif ((strpos($http_error, '404') > 0)) {
            $this->SendDebug('HTTP Response Header', 'Failure, location not found. Response Body: ' . $result, 0);
            $response = false;
        } elseif ((strpos($http_error, '500') > 0)) {
            $this->SendDebug('HTTP Response Header', 'Failure, internal error. Response Body: ' . $result, 0);
            $response = false;
        } elseif ((strpos($http_error, '502') > 0)) {
            $this->SendDebug('HTTP Response Header', 'Failure, backend error. Response Body: ' . $result, 0);
            $response = false;
        } elseif ((strpos($http_error, '415') > 0)) {
            $this->SendDebug('HTTP Response Header', 'Unsupported Media Type. Response Body: ' . $result, 0);
            $response = false;
        } else {
            $this->SendDebug('HTTP Response Header', $http_error . ' Response Body: ' . $result, 0);
            $response = false;
        }

        if ($result == '{"message":"Limit Exceeded"}') {
            $this->SendDebug('Gardena API', 'Limit Exceeded', 0);
            $this->WriteAttributeBoolean('limit', true);
        }
        return $response;
    }

    public function GetToken()
    {
        $token = $this->FetchAccessToken();
        return $token;
    }

    /**
     * This function will be called by the register button on the property page!
     */
    public function Register()
    {

        //Return everything which will open the browser
        return 'https://oauth.ipmagic.de/authorize/' . $this->oauthIdentifer . '?username=' . urlencode(IPS_GetLicensee());
    }

    /** Announce your desire to receive realtime events.
     * @return string
     */
    public function GetWebSocket()
    {
        $locationId = $this->ReadAttributeString('location_id');
        $this->SendDebug('Gardena Location ID', $locationId, 0);
        $response = false;
        $websocket_response = "";
        if ($locationId != '') {
            $service_id = 'request-12312'; // todo
            $payload = ['data' => [
                'id' => $service_id,
                'type' => 'WEBSOCKET',
                'attributes' => [
                    'locationId' => $locationId
                ]
            ]];

            $data = json_encode($payload);
            $websocket_response = $this->PostData(self::SMART_SYSTEM_BASE_URL . self::WEBSOCKET, $data);
            $response = true;
        }
        if ($response) {
            $websocket_data = json_decode($websocket_response, true);
            $url = $websocket_data['data']['attributes']['url'];
            $this->SendDebug('Gardena Websocket URL', $url, 0);
            $this->WriteAttributeString('websocket_url', $url);
            $parent = $ParentID = @IPS_GetInstance($this->InstanceID)['ConnectionID'];
            IPS_SetProperty($parent, 'URL', $url);
            IPS_SetProperty($parent, 'Active', true);
            IPS_ApplyChanges($parent);
        }
        return $websocket_response;
    }

    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString);
        $payload = $data->Buffer;
        $this->SendDebug('Receive Gardena Websocket Payload', $payload, 0);
        if ($payload != '[]') {
            $this->SendDataToChildren(json_encode(array("DataID" => "{E95D48A0-6A3D-3F4E-B73E-7645BBFC6A06}", "Buffer" => $payload)));
        }
    }

    private function PostData($url, $content)
    {

        $this->SendDebug("AT", $this->FetchAccessToken(), 0);
        $opts = array(
            "http" => array(
                "method" => "POST",
                "header" => "Authorization: Bearer " . $this->FetchAccessToken() . "\r\n" . 'Content-Type: application/vnd.api+json' . "\r\n"
                    . 'Content-Length: ' . strlen($content) . "\r\n",
                'content' => $content,
                "ignore_errors" => true
            )
        );
        $context = stream_context_create($opts);

        $result = file_get_contents($url, false, $context);
        $http_error = $http_response_header[0];
        $result = $this->GetErrorMessage($http_error, $result);
        return $result;
    }


    // GARDENA smart system API

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
            if (!$locations === false) {
                $snapshot = $this->RequestSnapshot();
            }
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
        if ($state_location === false) {
            $this->SendDebug('Gardena Locations', 'Could not get location', 0);
        } else {
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

    // Snapshot
    //Fetch current state of devices. Rate limited, so frequent polling not possible.

    public function ForwardData($data)
    {
        $data = json_decode($data);

        if (strlen($data->Payload) > 0) {
            $type = $data->Type;
            if ($type == 'PUT') {
                $this->SendDebug('ForwardData', $data->Endpoint . ', Payload: ' . $data->Payload, 0);
                $response = $this->PutData(self::SMART_SYSTEM_BASE_URL . $data->Endpoint, $data->Payload);
            } elseif ($type == 'POST') {
                $this->SendDebug('ForwardData', $data->Endpoint . ', Payload: ' . $data->Payload, 0);
                $response = $this->PostData(self::SMART_SYSTEM_BASE_URL . $data->Endpoint, $data->Payload);
            }
        } else {
            $this->SendDebug('ForwardData', $data->Endpoint, 0);
            if ($data->Endpoint == 'location_id') {
                $response = $this->ReadAttributeString('location_id');
            } elseif ($data->Endpoint == 'snapshot') {
                $response = $this->RequestSnapshot();
            } elseif ($data->Endpoint == 'snapshotbuffer') {
                $response = $this->RequestSnapshotBuffer();
            } elseif ($data->Endpoint == 'request_location_id') {
                $response = $this->RequestLocations();
            } elseif ($data->Endpoint == 'token') {
                $response = $this->CheckToken();
            }
        }
        return $response;
    }

    private function PutData($url, $content)
    {
        $this->SendDebug("AT", $this->FetchAccessToken(), 0);

        $opts = array(
            "http" => array(
                "method" => "PUT",
                "header" => "Authorization: Bearer " . $this->FetchAccessToken() . "\r\nContent-Type: application/json\r\n"
                    . 'Content-Length: ' . strlen($content) . "\r\n",
                'content' => $content,
                "ignore_errors" => true
            )
        );
        $context = stream_context_create($opts);

        $result = file_get_contents($url, false, $context);
        $http_error = $http_response_header[0];
        $result = $this->GetErrorMessage($http_error, $result);
        return $result;
    }

    public function RequestSnapshotBuffer()
    {
        // $this->WriteAttributeString('snapshot', '[]');
        $snapshot = $this->ReadAttributeString('snapshot');
        $this->SendDebug('Gardena Location Snapshot Buffer', $snapshot, 0);
        if ($snapshot == '[]') {
            $snapshot = $this->RequestSnapshot();
            $this->SendDebug('Gardena Request Snapshot', $snapshot, 0);
        }
        return $snapshot;
    }

    public function CheckToken()
    {
        $token = $this->ReadAttributeString('Token');
        return $token;
    }

    public function SetAlternativeAPI(bool $value)
    {
        $this->WriteAttributeBoolean('limit', $value);
        $this->WriteAttributeBoolean('alternative_url', $value);
        if ($value) {
            $this->SendDebug('Gardena alternative API', 'enabled', 0);
        } else {
            $this->SendDebug('Gardena alternative API', 'disabled', 0);
        }
    }

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
                'type'  => 'Image',
                'image' => 'data:image/png;base64, ' . self::PICTURE_LOGO_GARDENA],
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
                'type' => 'NumberSpinner',
                'suffix' => 'minutes',
                'minimum' => 15,
                'enabled' => true,
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

    /**
     * This function will be called by the OAuth control. Visibility should be protected!
     */
    protected function ProcessOAuthData()
    {
        $extended_debug = $this->ReadAttributeBoolean('extended_debug');
        if($extended_debug){
            //todo Debug shows no data
            $this->SendDebug('ProcessOAuthData', "Received Raw Data: " . print_r(file_get_contents('php://input'), true) , 0);
            $this->SendDebug('ProcessOAuthData', "Received GET Data: " . json_encode($_GET) , 0);
        }
        // <REDIRECT_URI>?code=<AUTHORIZATION_CODE>&state=<STATE>
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if (!isset($_GET['code'])) {
                die('Authorization Code expected');
            }
            if($extended_debug){
                $this->SendDebug('ProcessOAuthData', "Received Authorization Code: " . $_GET['code'], 0);
            }

            $token = $this->FetchRefreshToken($_GET['code']);
            if($token != false)
            {
                $this->SendDebug('ProcessOAuthData', "OK! Let's save the Refresh Token permanently", 0);

                $this->WriteAttributeString('Token', $token);

                //This will enforce a reload of the property page. change this in the future, when we have more dynamic forms
                IPS_ApplyChanges($this->InstanceID);
            }
        } else {
            //Just print raw post data!
            $payload = file_get_contents('php://input');
            $this->SendDebug('OAuth Response', $payload, 0);
        }
    }

    /** Exchange our Authentication Code for a permanent Refresh Token and a temporary Access Token
     * @param $code
     *
     * @return mixed
     */
    private function FetchRefreshToken($code)
    {
        $this->SendDebug('FetchRefreshToken', 'Use Authentication Code to get our precious Refresh Token!', 0);
        $result = false;
        set_error_handler(
            function ($severity, $message, $file, $line) {
                throw new ErrorException($message, $severity, $severity, $file, $line);
            }
        );

        try {
            $options = [
                'http' => [
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query(['code' => $code])]];
            $context = stream_context_create($options);
            $result = file_get_contents('https://oauth.ipmagic.de/access_token/' . $this->oauthIdentifer, false, $context);
        }
        catch (Exception $e) {
            $this->SendDebug('Error', $e->getMessage(), 0);
        }

        restore_error_handler();

        if($result != false)
        {
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
        else{
            return false;
        }
    }

    /***********************************************************
     * Configuration Form
     ***********************************************************/

    protected function GetErrorCodesMover($code)
    {
        // error codes mover
        $error_codes = [
            0 => $this->Translate('Unexpected error'),
            1 => $this->Translate('Outside working area'),
            2 => $this->Translate('No loop signal'),
            3 => $this->Translate('Wrong loop signal'),
            4 => $this->Translate('Loop sensor problem, front'),
            5 => $this->Translate('Loop sensor problem, rear'),
            6 => $this->Translate('Loop sensor problem, left'),
            7 => $this->Translate('Loop sensor problem, right'),
            8 => $this->Translate('Wrong PIN code'),
            9 => $this->Translate('Trapped'),
            10 => $this->Translate('Upside down'),
            11 => $this->Translate('Low battery'),
            12 => $this->Translate('Empty battery'),
            13 => $this->Translate('No drive'),
            14 => $this->Translate('Mower lifted'),
            15 => $this->Translate('Lifted'),
            16 => $this->Translate('Stuck in charging station'),
            17 => $this->Translate('Charging station blocked'),
            18 => $this->Translate('Collision sensor problem, rear'),
            19 => $this->Translate('Collision sensor problem, front'),
            20 => $this->Translate('Wheel motor blocked, right'),
            21 => $this->Translate('Wheel motor blocked, left'),
            22 => $this->Translate('Wheel drive problem, right'),
            23 => $this->Translate('Wheel drive problem, left'),
            24 => $this->Translate('Cutting system blocked'),
            25 => $this->Translate('Cutting system blocked'),
            26 => $this->Translate('Invalid sub-device combination'),
            27 => $this->Translate('Settings restored'),
            28 => $this->Translate('Memory circuit problem'),
            29 => $this->Translate('Slope too steep'),
            30 => $this->Translate('Charging system problem'),
            31 => $this->Translate('STOP button problem'),
            32 => $this->Translate('Tilt sensor problem'),
            33 => $this->Translate('Mower tilted'),
            34 => $this->Translate('Cutting stopped - slope too steep'),
            35 => $this->Translate('Wheel motor overloaded, right'),
            36 => $this->Translate('Wheel motor overloaded, left'),
            37 => $this->Translate('Charging current too high'),
            38 => $this->Translate('Electronic problem'),
            39 => $this->Translate('Cutting motor problem'),
            40 => $this->Translate('Limited cutting height range'),
            41 => $this->Translate('Unexpected cutting height adj'),
            42 => $this->Translate('Limited cutting height range'),
            43 => $this->Translate('Cutting height problem, drive'),
            44 => $this->Translate('Cutting height problem, curr'),
            45 => $this->Translate('Cutting height problem, dir'),
            46 => $this->Translate('Cutting height blocked'),
            47 => $this->Translate('Cutting height problem'),
            48 => $this->Translate('No response from charger'),
            49 => $this->Translate('Ultrasonic problem'),
            50 => $this->Translate('Guide 1 not found'),
            51 => $this->Translate('Guide 2 not found'),
            52 => $this->Translate('Guide 3 not found'),
            53 => $this->Translate('GPS navigation problem'),
            54 => $this->Translate('Weak GPS signal'),
            55 => $this->Translate('Difficult finding home'),
            56 => $this->Translate('Guide calibration accomplished'),
            57 => $this->Translate('Guide calibration failed'),
            58 => $this->Translate('Temporary battery problem'),
            59 => $this->Translate('Temporary battery problem'),
            60 => $this->Translate('Temporary battery problem'),
            61 => $this->Translate('Temporary battery problem'),
            62 => $this->Translate('Temporary battery problem'),
            63 => $this->Translate('Temporary battery problem'),
            64 => $this->Translate('Temporary battery problem'),
            65 => $this->Translate('Temporary battery problem'),
            66 => $this->Translate('Battery problem'),
            67 => $this->Translate('Battery problem'),
            68 => $this->Translate('Temporary battery problem'),
            69 => $this->Translate('Alarm! Mower switched off'),
            70 => $this->Translate('Alarm! Mower stopped'),
            71 => $this->Translate('Alarm! Mower lifted'),
            72 => $this->Translate('Alarm! Mower tilted'),
            73 => $this->Translate('Alarm! Mower in motion'),
            74 => $this->Translate('Alarm! Outside geofence'),
            75 => $this->Translate('Connection changed'),
            76 => $this->Translate('Connection NOT changed'),
            77 => $this->Translate('Com board not available'),
            78 => $this->Translate('Slipped - Mower has Slipped.Situation not solved with moving pattern'),
            79 => $this->Translate('Invalid battery combination - Invalid combination of different battery types.'),
            80 => $this->Translate('Cutting system imbalance    Warning'),
            81 => $this->Translate('Safety function faulty'),
            82 => $this->Translate('Wheel motor blocked, rear right'),
            83 => $this->Translate('Wheel motor blocked, rear left'),
            84 => $this->Translate('Wheel drive problem, rear right'),
            85 => $this->Translate('Wheel drive problem, rear left'),
            86 => $this->Translate('Wheel motor overloaded, rear right'),
            87 => $this->Translate('Wheel motor overloaded, rear left'),
            88 => $this->Translate('Angular sensor problem'),
            89 => $this->Translate('Invalid system configuration'),
            90 => $this->Translate('No power in charging station')
        ];
        return $error_codes[$code];
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
}