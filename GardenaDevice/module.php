<?php
declare(strict_types=1);

require_once(__DIR__ . "/../bootstrap.php");
require_once __DIR__ . '/../libs/ProfileHelper.php';
require_once __DIR__ . '/../libs/ConstHelper.php';

class GardenaDevice extends IPSModule
{
    use ProfileHelper;

    // helper properties
    private $position = 0;

    private const GARDENA_smart_Irrigation_Control = 'GARDENA smart Irrigation Control';
    private const GARDENA_smart_Sensor = 'GARDENA smart Sensor';

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent('{9775D7CA-5667-8554-0172-2EBB2F553A54}');

        $this->RegisterPropertyString('id', '');
        $this->RegisterPropertyString('name', '');
        $this->RegisterPropertyString('serial', '');
        $this->RegisterPropertyString('model_type', '');
        $this->RegisterAttributeBoolean('VALVE_1_STATE', false);
        $this->RegisterAttributeBoolean('VALVE_1_STATE_enabled', false);
        $this->RegisterAttributeBoolean('VALVE_2_STATE', false);
        $this->RegisterAttributeBoolean('VALVE_2_STATE_enabled', false);
        $this->RegisterAttributeBoolean('VALVE_3_STATE', false);
        $this->RegisterAttributeBoolean('VALVE_3_STATE_enabled', false);
        $this->RegisterAttributeBoolean('VALVE_4_STATE', false);
        $this->RegisterAttributeBoolean('VALVE_4_STATE_enabled', false);
        $this->RegisterAttributeBoolean('VALVE_5_STATE', false);
        $this->RegisterAttributeBoolean('VALVE_5_STATE_enabled', false);
        $this->RegisterAttributeBoolean('VALVE_6_STATE', false);
        $this->RegisterAttributeBoolean('VALVE_6_STATE_enabled', false);
        $this->RegisterAttributeInteger('BATTERY_LEVEL', 0);
        $this->RegisterAttributeString('BATTERY_LEVEL_TIMESTAMP', '');
        $this->RegisterAttributeBoolean('BATTERY_LEVEL_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('BATTERY_STATE', '');
        $this->RegisterAttributeString('BATTERY_STATE_TIMESTAMP', '');
        $this->RegisterAttributeBoolean('BATTERY_STATE_TIMESTAMP_enabled', false);
        $this->RegisterAttributeInteger('RF_LINK_LEVEL', 0);
        $this->RegisterAttributeString('RF_LINK_LEVEL_TIMESTAMP', '');
        $this->RegisterAttributeBoolean('RF_LINK_LEVEL_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('RF_LINK_STATE', '');
        $this->RegisterAttributeBoolean('RF_LINK_STATE_enabled', false);
        $this->RegisterAttributeInteger('soil_humidity', 0);
        $this->RegisterAttributeString('soil_humidity_timestamp', '');
        $this->RegisterAttributeBoolean('soil_humidity_timestamp_enabled', false);
        $this->RegisterAttributeFloat('soil_temperature', 0);
        $this->RegisterAttributeString('soil_temperature_timestamp', '');
        $this->RegisterAttributeBoolean('soil_temperature_timestamp_enabled', false);
        $this->RegisterAttributeFloat('ambient_temperature', 0);
        $this->RegisterAttributeString('ambient_temperature_timestamp', '');
        $this->RegisterAttributeBoolean('ambient_temperature_timestamp_enabled', false);
        $this->RegisterAttributeInteger('light_intensity', 0);
        $this->RegisterAttributeString('light_intensity_timestamp', '');
        $this->RegisterAttributeBoolean('light_intensity_timestamp_enabled', false);

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
        $this->ValidateConfiguration();
    }

    private function ValidateConfiguration()
    {
        $id = $this->ReadPropertyString('id');
        if($id == '')
        {
            $this->SetStatus(205);
        }
        elseif($id != '')
        {
            $this->RegisterVariables();
            $this->SetStatus(IS_ACTIVE);
        }
    }

    private function CheckRequest()
    {
        $id = $this->ReadPropertyString('id');
        $data = false;
        if($id == '')
        {
            $this->SetStatus(205);
        }
        elseif($id != '')
        {
            $data = $this->RequestStatus('snapshot');
        }
        return $data;
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

    private function RegisterVariables(): void
    {
        $model_type = $this->ReadPropertyString('model_type');
        $this->GetDeviceStatus();
        if($model_type == self::GARDENA_smart_Irrigation_Control)
        {
            $this->SetupVariable(
                'VALVE_1_STATE', $this->Translate('valve 1'), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
            );
            $this->SetupVariable(
                'VALVE_2_STATE', $this->Translate('valve 2'), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
            );
            $this->SetupVariable(
                'VALVE_3_STATE', $this->Translate('valve 3'), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
            );
            $this->SetupVariable(
                'VALVE_4_STATE', $this->Translate('valve 4'), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
            );
            $this->SetupVariable(
                'VALVE_5_STATE', $this->Translate('valve 5'), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
            );
            $this->SetupVariable(
                'VALVE_6_STATE', $this->Translate('valve 6'), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
            );
            /*
            $this->SetupVariable(
                'RF_LINK_STATE', $this->Translate('rf link state'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
            );
            */
        }

        if($model_type == self::GARDENA_smart_Sensor)
        {
            $this->SetupVariable(
                'BATTERY_LEVEL', $this->Translate('battery level'), '~Battery.100', $this->_getPosition(), VARIABLETYPE_INTEGER, false, true
            );
            $this->SetupVariable(
                'BATTERY_LEVEL_TIMESTAMP', $this->Translate('battery level timestamp'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
            );

            $this->SetupVariable(
                'BATTERY_STATE', $this->Translate('battery state'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, true
            );
            $this->SetupVariable(
                'BATTERY_STATE_TIMESTAMP', $this->Translate('battery state timestamp'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
            );

            $this->SetupVariable(
                'RF_LINK_LEVEL', $this->Translate('rf link level'), '~Intensity.100', $this->_getPosition(), VARIABLETYPE_INTEGER, false, true
            );
            $this->SetupVariable(
                'RF_LINK_LEVEL_TIMESTAMP', $this->Translate('rf link level timestamp'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
            );
            /*
            $this->SetupVariable(
                'RF_LINK_STATE', $this->Translate('rf link state'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
            );
            */

            $this->SetupVariable(
                'soil_humidity', $this->Translate('soil humidity'), '~Humidity', $this->_getPosition(), VARIABLETYPE_INTEGER, false, true
            );
            $this->SetupVariable(
                'soil_humidity_timestamp', $this->Translate('soil humidity timestamp'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
            );

            $this->SetupVariable(
                'soil_temperature', $this->Translate('soil temperature'), '~Temperature', $this->_getPosition(), VARIABLETYPE_FLOAT, false, true
            );
            $this->SetupVariable(
                'soil_temperature_timestamp', $this->Translate('soil temperature timestamp'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
            );

            $this->SetupVariable(
                'ambient_temperature', $this->Translate('ambient temperature'), '~Temperature', $this->_getPosition(), VARIABLETYPE_FLOAT, false, true
            );
            $this->SetupVariable(
                'ambient_temperature_timestamp', $this->Translate('ambient temperature timestamp'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
            );

            $this->SetupVariable(
                'light_intensity', $this->Translate('light intensity'), '~Illumination', $this->_getPosition(), VARIABLETYPE_INTEGER, false, true
            );
            $this->SetupVariable(
                'light_intensity_timestamp', $this->Translate('light intensity timestamp'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
            );
        }
        $this->WriteValues();

        /*
        $this->RegisterProfile('Gardena.Brightness', 'Intensity', '', ' %', 0, 200, 1, 0, VARIABLETYPE_INTEGER);
        */
    }

    /** Variable anlegen / löschen
     *
     * @param $ident
     * @param $name
     * @param $profile
     * @param $position
     * @param $vartype
     * @param $visible
     *
     * @return bool|int
     */
    protected function SetupVariable($ident, $name, $profile, $position, $vartype, $enableaction, $visible = false)
    {
        $objid = false;
        if ($visible) {
            $this->SendDebug('Gardena Variable:', 'Variable with Ident ' . $ident . ' is visible', 0);
        } else {
            $visible = $this->ReadAttributeBoolean($ident . '_enabled');
            $this->SendDebug('Gardena Variable:', 'Variable with Ident ' . $ident . ' is shown' . print_r($visible, true), 0);
        }
        if ($visible == true) {
            switch ($vartype) {
                case VARIABLETYPE_BOOLEAN:
                    $objid = $this->RegisterVariableBoolean($ident, $name, $profile, $position);
                    $value = $this->ReadAttributeBoolean($ident);
                    $this->SetValue($ident, $value);
                    break;
                case VARIABLETYPE_INTEGER:
                    $objid = $this->RegisterVariableInteger($ident, $name, $profile, $position);
                    $value = $this->ReadAttributeInteger($ident);
                    $this->SetValue($ident, $value);
                    break;
                case VARIABLETYPE_FLOAT:
                    $objid = $this->RegisterVariableFloat($ident, $name, $profile, $position);
                    $value = $this->ReadAttributeFloat($ident);
                    $this->SetValue($ident, $value);
                    break;
                case VARIABLETYPE_STRING:
                    $objid = $this->RegisterVariableString($ident, $name, $profile, $position);
                    $value = $this->ReadAttributeString($ident);
                    $this->SetValue($ident, $value);
                    break;
            }
            if ($enableaction) {
                $this->EnableAction($ident);
            }
        } else {
            $objid = @$this->GetIDForIdent($ident);
            if ($objid > 0) {
                $this->UnregisterVariable($ident);
            }
        }
        return $objid;
    }



    /** @noinspection PhpMissingParentCallCommonInspection */
    public function RequestAction($Ident, $Value)
    {
        if ($Ident === 'VALVE_1_STATE') {
            $this->ToggleValve($Value, 1);
        }
        if ($Ident === 'VALVE_2_STATE') {
            $this->ToggleValve($Value, 2);
        }
        if ($Ident === 'VALVE_3_STATE') {
            $this->ToggleValve($Value, 3);
        }
        if ($Ident === 'VALVE_4_STATE') {
            $this->ToggleValve($Value, 4);
        }
        if ($Ident === 'VALVE_5_STATE') {
            $this->ToggleValve($Value, 5);
        }
        if ($Ident === 'VALVE_6_STATE') {
            $this->ToggleValve($Value, 6);
        }
    }

    // GARDENA smart system API

    /** Fetch current state of devices and then get subsequent updates in realtime.
     * POST
     */
    public function FetchCurrentState()
    {
        $locationId = $this->RequestStatus('location_id');
        $this->SendDebug('Gardena Location ID', $locationId, 0);
        // $payload = json_decode($snapshot, true);
        $service_id = '';
        $payload = ['data' => [
            'id'=> $service_id,
            'type'=> 'WEBSOCKET',
            'attributes'=> [
                'locationId'=> $locationId
            ]
        ]];
        $data = json_encode($payload);
        $result = json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{0FE98840-1BBA-4E87-897D-30506FEF540A}',
            'Type' => 'POST',
            'Endpoint' => '/websocket',
            'Payload'  => $data
        ])));
        return $result;
    }

    /** Control behaviour of devices.
     * PUT
     */
    public function ControlDevice(string $service_id, string $type, string $command, string $parameter)
    {
        $payload = ['data' => [
            'id'=> $service_id,
            'type'=> $type,
            'attributes'=> [
                'command'=> $command,
                'seconds'=> $parameter
            ]
        ]];
        $data = json_encode($payload);
        $this->SendCommand($service_id, $data);
    }

    public function ToggleValve(bool $state, int $index)
    {
        if($state)
        {
            $this->OpenValve($index);
        }
        else{
            $this->StopValve($index);
        }
    }

    /** START
     * manual operation, use 'seconds' attribute to define
     */
    public function OpenValve(int $index)
    {
        $id = $this->GetValveID($index);
        $this->ControlDevice($id,'VALVE_CONTROL', 'START_SECONDS_TO_OVERRIDE', "3600");
    }

    public function StopValve(int $index)
    {
        $id = $this->GetValveID($index);
        $this->ControlDevice($id,'VALVE_CONTROL', 'STOP_UNTIL_NEXT_TASK', "0");
    }

    public function PauseValve(int $index)
    {
        $id = $this->GetValveID($index);
        $this->ControlDevice($id, 'VALVE_CONTROL', 'PAUSE', "0");
    }

    public function UnpauseValve(int $index)
    {
        $id = $this->GetValveID($index);
        $this->ControlDevice($id,'VALVE_CONTROL', 'UNPAUSE', "0");
    }

    private function GetValveID($index)
    {
        return $this->ReadAttributeString('Valve_ID_' . $index);
    }

    /** Get Device Type
     * @param $device
     */
    private function GetDeviceData($device)
    {
        $id_instance = $this->ReadPropertyString('id');
        $model_type_instance = $this->ReadPropertyString('model_type');
        $model_type = $device['attributes']['modelType']['value'];
        $id = $device['id'];
        if($model_type == $model_type_instance && $id == $id_instance)
        {
            if ($model_type == 'GARDENA smart Irrigation Control') {
                $this->GetIrrigationControlData($device);
            } elseif ($model_type == 'GARDENA smart Sensor') {
                $this->GetSensorInfo($device);
            }
        }
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
        $this->WriteAttributeInteger('BATTERY_LEVEL', $battery_level);
        $battery_level_timestamp = $device['attributes']['batteryLevel']['timestamp'];
        $this->SendDebug('Gardena Device ' . $name, 'battery level timestamp: ' . $battery_level_timestamp, 0);
        $this->WriteAttributeString('BATTERY_LEVEL_TIMESTAMP', $battery_level_timestamp);
        $battery_state = $device['attributes']['batteryState']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'battery state: ' . $battery_state, 0);
        $this->WriteAttributeString('BATTERY_STATE', $battery_state);
        $battery_state_timestamp = $device['attributes']['batteryState']['timestamp'];
        $this->SendDebug('Gardena Device ' . $name, 'battery state timestamp: ' . $battery_state_timestamp, 0);
        $this->WriteAttributeString('BATTERY_STATE_TIMESTAMP', $battery_state_timestamp);
        $rf_link_level = $device['attributes']['rfLinkLevel']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'RF link level: ' . $rf_link_level . '%', 0);
        $this->WriteAttributeInteger('RF_LINK_LEVEL', $rf_link_level);
        $rf_link_level_timestamp = $device['attributes']['rfLinkLevel']['timestamp'];
        $this->SendDebug('Gardena Device ' . $name, 'RF link level timestamp: ' . $rf_link_level_timestamp, 0);
        $this->WriteAttributeString('RF_LINK_LEVEL_TIMESTAMP', $rf_link_level_timestamp);
        $serial = $device['attributes']['serial']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'serial: ' . $serial, 0);
        $rf_link_state = $device['attributes']['rfLinkState']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'RF link state: ' . $rf_link_state, 0);
        // $this->WriteAttributeString('RF_LINK_STATE', $rf_link_state);

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
        // $this->WriteAttributeString('RF_LINK_STATE', $rf_link_state);
        return ['id' => $id, 'name' => $name, 'serial' => $serial, 'rf_link_state' => $rf_link_state];
    }

    private function GetSensorData($device)
    {
        $soil_humidity = $device['attributes']['soilHumidity']['value'];
        $this->SendDebug('Gardena Sensor Humidity', $soil_humidity . ' %', 0);
        $this->WriteAttributeInteger('soil_humidity', $soil_humidity);
        $soil_humidity_timestamp = $device['attributes']['soilHumidity']['timestamp'];
        $this->SendDebug('Gardena Sensor Humidity Timestamp', $soil_humidity_timestamp, 0);
        $this->WriteAttributeString('soil_humidity_timestamp', $soil_humidity_timestamp);
        $soil_temperature = $device['attributes']['soilTemperature']['value'];
        $this->SendDebug('Gardena Sensor Temperature', $soil_temperature . ' °C', 0);
        $this->WriteAttributeFloat('soil_temperature', $soil_temperature);
        $soil_temperature_timestamp = $device['attributes']['soilTemperature']['timestamp'];
        $this->SendDebug('Gardena Sensor Temperature Timestamp', $soil_temperature_timestamp, 0);
        $this->WriteAttributeString('soil_temperature_timestamp', $soil_temperature_timestamp);
        $ambient_temperature = $device['attributes']['ambientTemperature']['value'];
        $this->SendDebug('Gardena Sensor Ambient Temperature', $ambient_temperature . ' °C', 0);
        $this->WriteAttributeFloat('ambient_temperature', $ambient_temperature);
        $ambient_temperature_timestamp = $device['attributes']['ambientTemperature']['timestamp'];
        $this->SendDebug('Gardena Sensor Ambient Temperature Timestamp', $ambient_temperature_timestamp, 0);
        $this->WriteAttributeString('ambient_temperature_timestamp', $ambient_temperature_timestamp);
        $light_intensity = $device['attributes']['lightIntensity']['value'];
        $this->SendDebug('Gardena Sensor Light Intensity', $light_intensity . ' lx', 0);
        $this->WriteAttributeInteger('light_intensity', $light_intensity);
        $light_intensity_timestamp = $device['attributes']['lightIntensity']['timestamp'];
        $this->SendDebug('Gardena Sensor Light Intensity Timestamp', $light_intensity_timestamp, 0);
        $this->WriteAttributeString('light_intensity_timestamp', $light_intensity_timestamp);
    }





    private function GetDeviceStatus()
    {
        $snapshot = $this->RequestStatus('snapshot');
        if($snapshot != '[]')
        {
            $this->CheckDeviceType($snapshot);
        }
    }

    private function CheckDeviceType($snapshot)
    {
        $payload = json_decode($snapshot, true);
        if (!empty($snapshot)) {
            $included = $payload['included'];
            foreach ($included as $device) {
                $type = $device['type'];
                if ($type == 'COMMON') {
                    $this->GetDeviceData($device);
                }
                if ($type == 'SENSOR') {
                    $this->GetSensorData($device);
                }
            }
        }
    }

    private function WriteEnabledValue($ident, $vartype, $enabled = false)
    {
        if($enabled)
        {
            $value_enabled = true;
        }
        else{
            $value_enabled = $this->ReadAttributeBoolean($ident . '_enabled');
        }

        if($value_enabled)
        {
            switch ($vartype) {
                case VARIABLETYPE_BOOLEAN:
                    $value = $this->ReadAttributeBoolean($ident);
                    break;
                case VARIABLETYPE_INTEGER:
                    $value = $this->ReadAttributeInteger($ident);
                    break;
                case VARIABLETYPE_FLOAT:
                    $value = $this->ReadAttributeFloat($ident);
                    break;
                case VARIABLETYPE_STRING:
                    $value = $this->ReadAttributeString($ident);
                    break;
            }
            $this->SetValue($ident, $value);
        }
    }

    private function WriteValues()
    {
        $model_type_instance = $this->ReadPropertyString('model_type');
        if($model_type_instance == self::GARDENA_smart_Irrigation_Control)
        {
            $this->SendDebug('Gardena Request Response', self::GARDENA_smart_Irrigation_Control, 0);
            // $this->WriteEnabledValue('RF_LINK_STATE', VARIABLETYPE_STRING, true);
        }

        if($model_type_instance == self::GARDENA_smart_Sensor)
        {
            $this->SendDebug('Gardena Write Values', self::GARDENA_smart_Sensor, 0);
            $this->WriteEnabledValue('BATTERY_LEVEL', VARIABLETYPE_INTEGER, true);
            $this->WriteEnabledValue('BATTERY_LEVEL_TIMESTAMP', VARIABLETYPE_STRING);
            $this->WriteEnabledValue('BATTERY_STATE', VARIABLETYPE_STRING, true);
            $this->WriteEnabledValue('BATTERY_STATE_TIMESTAMP', VARIABLETYPE_STRING);
            $this->WriteEnabledValue('RF_LINK_LEVEL', VARIABLETYPE_INTEGER, true);
            $this->WriteEnabledValue('RF_LINK_LEVEL_TIMESTAMP', VARIABLETYPE_STRING);
            // $this->WriteEnabledValue('RF_LINK_STATE', VARIABLETYPE_STRING, true);
            $this->WriteEnabledValue('soil_humidity', VARIABLETYPE_INTEGER, true);
            $this->WriteEnabledValue('soil_humidity_timestamp', VARIABLETYPE_STRING);
            $this->WriteEnabledValue('soil_temperature', VARIABLETYPE_FLOAT, true);
            $this->WriteEnabledValue('soil_temperature_timestamp', VARIABLETYPE_STRING);
            $this->WriteEnabledValue('ambient_temperature', VARIABLETYPE_FLOAT, true);
            $this->WriteEnabledValue('ambient_temperature_timestamp', VARIABLETYPE_STRING);
            $this->WriteEnabledValue('light_intensity', VARIABLETYPE_INTEGER, true);
            $this->WriteEnabledValue('light_intensity_timestamp', VARIABLETYPE_STRING);
        }
    }

    public function RequestStatus(string $endpoint)
    {
        $data = $this->SendDataToParent(json_encode([
            'DataID'   => '{0FE98840-1BBA-4E87-897D-30506FEF540A}',
            'Type' => 'GET',
            'Endpoint' => $endpoint,
            'Payload'  => ''
        ]));
        $this->SendDebug('Gardena Request Response', $data, 0);
        return $data;
    }
	
	public function ReceiveData($JSONString)
		{
			$data = json_decode($JSONString);
			$snapshot = $data->Buffer;
            $this->SendDebug('Receive Snapshot', $snapshot, 0);
            if($snapshot != '[]')
            {
                $this->CheckDeviceType($snapshot);
            }
		}

    public function SendCommand(string $service_id, string $data)
    {
        $result = $this->SendDataToParent(json_encode([
            'DataID'   => '{0FE98840-1BBA-4E87-897D-30506FEF540A}',
            'Type' => 'PUT',
            'Endpoint' => '/command/' . $service_id,
            'Payload'  => $data
        ]));
        return $result;
    }

    public function SetWebFrontVariable(string $ident, bool $value)
    {
        $this->WriteAttributeBoolean($ident, $value);
        if($value)
        {
            $this->SendDebug('Gardena Webfront Variable', $ident . ' enabled', 0);
        }
        else{
            $this->SendDebug('Gardena Webfront Variable', $ident . ' disabled', 0);
        }

        $this->RegisterVariables();
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
        return json_encode([
                               'elements' => $this->FormHead(),
                               'actions' => $this->FormActions(),
                               'status' => $this->FormStatus()
                           ]);
    }

    /**
     * return form configurations on configuration step
     * @return array
     */
    protected function FormHead()
    {
        $data = $this->CheckRequest();
        if($data != false)
        {
            $form = [
                [
                    'type' => 'Label',
                    'label' => $this->ReadPropertyString('name')
                ],
                [
                    'type' => 'Label',
                    'label' => $this->Translate('serial number: ') . $this->ReadPropertyString('serial')
                ],
                [
                    'type' => 'Label',
                    'label' => $this->Translate('type: ') . $this->Translate($this->ReadPropertyString('model_type'))
                ]
            ];
        }
        else
        {
            $form = [
                [
                    'type' => 'Label',
                    'label' => 'This device can only created by the Gardena configurator, please use the Gardena configurator for creating Gardena devices.'
                ]
            ];
        }
        return $form;
    }

    /**
     * return form actions by token
     * @return array
     */
    protected function FormActions()
    {


        $model_type = $this->ReadPropertyString('model_type');
        $form = [];
        if ($model_type == self::GARDENA_smart_Irrigation_Control) {
            $form = [
                [
                    'name'     => 'VALVE_1_STATE_enabled',
                    'type'     => 'CheckBox',
                    'caption'  => 'valve 1',
                    'visible'  => true,
                    'value'    => $this->ReadAttributeBoolean('VALVE_1_STATE_enabled'),
                    'onChange' => 'Gardena_SetWebFrontVariable($id, "VALVE_1_STATE_enabled", $VALVE_1_STATE_enabled);'],
                [
                    'name'     => 'VALVE_2_STATE_enabled',
                    'type'     => 'CheckBox',
                    'caption'  => 'valve 2',
                    'visible'  => true,
                    'value'    => $this->ReadAttributeBoolean('VALVE_2_STATE_enabled'),
                    'onChange' => 'Gardena_SetWebFrontVariable($id, "VALVE_2_STATE_enabled", $VALVE_2_STATE_enabled);'],
                [
                    'name'     => 'VALVE_3_STATE_enabled',
                    'type'     => 'CheckBox',
                    'caption'  => 'valve 3',
                    'visible'  => true,
                    'value'    => $this->ReadAttributeBoolean('VALVE_3_STATE_enabled'),
                    'onChange' => 'Gardena_SetWebFrontVariable($id, "VALVE_3_STATE_enabled", $VALVE_3_STATE_enabled);'],
                [
                    'name'     => 'VALVE_4_STATE_enabled',
                    'type'     => 'CheckBox',
                    'caption'  => 'valve 4',
                    'visible'  => true,
                    'value'    => $this->ReadAttributeBoolean('VALVE_4_STATE_enabled'),
                    'onChange' => 'Gardena_SetWebFrontVariable($id, "VALVE_4_STATE_enabled", $VALVE_4_STATE_enabled);'],
                [
                    'name'     => 'VALVE_5_STATE_enabled',
                    'type'     => 'CheckBox',
                    'caption'  => 'valve 5',
                    'visible'  => true,
                    'value'    => $this->ReadAttributeBoolean('VALVE_5_STATE_enabled'),
                    'onChange' => 'Gardena_SetWebFrontVariable($id, "VALVE_5_STATE_enabled", $VALVE_5_STATE_enabled);'],
                [
                    'name'     => 'VALVE_6_STATE_enabled',
                    'type'     => 'CheckBox',
                    'caption'  => 'valve 6',
                    'visible'  => true,
                    'value'    => $this->ReadAttributeBoolean('VALVE_6_STATE_enabled'),
                    'onChange' => 'Gardena_SetWebFrontVariable($id, "VALVE_6_STATE_enabled", $VALVE_6_STATE_enabled);'],
                [
                    'name'     => 'RF_LINK_STATE_enabled',
                    'type'     => 'CheckBox',
                    'caption'  => 'rf link state',
                    'visible'  => true,
                    'value'    => $this->ReadAttributeBoolean('RF_LINK_STATE_enabled'),
                    'onChange' => 'Gardena_SetWebFrontVariable($id, "RF_LINK_STATE_enabled", $RF_LINK_STATE_enabled);']
            ];
        } elseif ($model_type == self::GARDENA_smart_Sensor) {
            $form = [
                [
                    'name'     => 'BATTERY_LEVEL_TIMESTAMP_enabled',
                    'type'     => 'CheckBox',
                    'caption'  => 'battery level timestamp',
                    'visible'  => true,
                    'value'    => $this->ReadAttributeBoolean('BATTERY_LEVEL_TIMESTAMP_enabled'),
                    'onChange' => 'Gardena_SetWebFrontVariable($id, "BATTERY_LEVEL_TIMESTAMP_enabled", $BATTERY_LEVEL_TIMESTAMP_enabled);'],
                [
                    'name'     => 'BATTERY_STATE_TIMESTAMP_enabled',
                    'type'     => 'CheckBox',
                    'caption'  => 'battery state timestamp',
                    'visible'  => true,
                    'value'    => $this->ReadAttributeBoolean('BATTERY_STATE_TIMESTAMP_enabled'),
                    'onChange' => 'Gardena_SetWebFrontVariable($id, "BATTERY_STATE_TIMESTAMP_enabled", $BATTERY_STATE_TIMESTAMP_enabled);'],
                [
                    'name'     => 'RF_LINK_STATE_enabled',
                    'type'     => 'CheckBox',
                    'caption'  => 'rf link state',
                    'visible'  => true,
                    'value'    => $this->ReadAttributeBoolean('RF_LINK_STATE_enabled'),
                    'onChange' => 'Gardena_SetWebFrontVariable($id, "RF_LINK_STATE_enabled", $RF_LINK_STATE_enabled);'],
                [
                    'name'     => 'RF_LINK_LEVEL_TIMESTAMP_enabled',
                    'type'     => 'CheckBox',
                    'caption'  => 'rf link level timestamp',
                    'visible'  => true,
                    'value'    => $this->ReadAttributeBoolean('RF_LINK_LEVEL_TIMESTAMP_enabled'),
                    'onChange' => 'Gardena_SetWebFrontVariable($id, "RF_LINK_LEVEL_TIMESTAMP_enabled", $RF_LINK_LEVEL_TIMESTAMP_enabled);'],
                [
                    'name'     => 'soil_humidity_timestamp_enabled',
                    'type'     => 'CheckBox',
                    'caption'  => 'soil humidity timestamp',
                    'visible'  => true,
                    'value'    => $this->ReadAttributeBoolean('soil_humidity_timestamp_enabled'),
                    'onChange' => 'Gardena_SetWebFrontVariable($id, "soil_humidity_timestamp_enabled", $soil_humidity_timestamp_enabled);'],
                [
                    'name'     => 'soil_temperature_timestamp_enabled',
                    'type'     => 'CheckBox',
                    'caption'  => 'soil temperature timestamp',
                    'visible'  => true,
                    'value'    => $this->ReadAttributeBoolean('soil_temperature_timestamp_enabled'),
                    'onChange' => 'Gardena_SetWebFrontVariable($id, "soil_temperature_timestamp_enabled", $soil_temperature_timestamp_enabled);'],
                [
                    'name'     => 'ambient_temperature_timestamp_enabled',
                    'type'     => 'CheckBox',
                    'caption'  => 'ambient temperature timestamp',
                    'visible'  => true,
                    'value'    => $this->ReadAttributeBoolean('ambient_temperature_timestamp_enabled'),
                    'onChange' => 'Gardena_SetWebFrontVariable($id, "ambient_temperature_timestamp_enabled", $ambient_temperature_timestamp_enabled);'],
                [
                    'name'     => 'light_intensity_timestamp_enabled',
                    'type'     => 'CheckBox',
                    'caption'  => 'light intensity timestamp',
                    'visible'  => true,
                    'value'    => $this->ReadAttributeBoolean('light_intensity_timestamp_enabled'),
                    'onChange' => 'Gardena_SetWebFrontVariable($id, "light_intensity_timestamp_enabled", $light_intensity_timestamp_enabled);']
            ];
        }

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
                'caption' => 'Gardena device created.'
            ],
            [
                'code' => IS_INACTIVE,
                'icon' => 'inactive',
                'caption' => 'interface closed.'
            ],
            [
                'code' => 205,
                'icon' => 'error',
                'caption' => 'This device can only created by the Gardena configurator, please use the Gardena configurator for creating Gardena devices.'
            ]
        ];

        return $form;
    }

    /**
     * return incremented position
     * @return int
     */
    private function _getPosition()
    {
        $this->position++;
        return $this->position;
    }
}
