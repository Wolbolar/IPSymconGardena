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

    private const GARDENA_smart_Water_Control = 'GARDENA smart Water Control';
    private const GARDENA_smart_Irrigation_Control = 'GARDENA smart Irrigation Control';
    private const GARDENA_smart_Sensor = 'GARDENA smart Sensor';
    private const PICTURE_LOGO_GARDENA = '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';

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent('{9775D7CA-5667-8554-0172-2EBB2F553A54}');

        $this->RegisterPropertyString('id', '');
        $this->RegisterPropertyString('name', '');
        $this->RegisterAttributeBoolean('name_enabled', false);
        $this->RegisterPropertyString('serial', '');
        $this->RegisterAttributeBoolean('serial_enabled', false);
        $this->RegisterPropertyString('model_type', '');
        $this->RegisterAttributeString('VALVE_WATERCONTROL_NAME', '');
        $this->RegisterAttributeString('VALVE_WATERCONTROL_ACTIVITY', '');
        $this->RegisterAttributeBoolean('VALVE_WATERCONTROL_ACTIVITY_enabled', false);
        $this->RegisterAttributeBoolean('VALVE_WATERCONTROL_ACTIVITY_STATE', false);
        $this->RegisterAttributeBoolean('VALVE_WATERCONTROL_ACTIVITY_STATE_enabled', false);
        $this->RegisterAttributeInteger('VALVE_WATERCONTROL_ACTIVITY_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_WATERCONTROL_ACTIVITY_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('VALVE_WATERCONTROL_STATE', '');
        $this->RegisterAttributeBoolean('VALVE_WATERCONTROL_STATE_enabled', false);
        $this->RegisterAttributeInteger('VALVE_WATERCONTROL_STATE_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_WATERCONTROL_STATE_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('VALVE_WATERCONTROL_ERRORCODE', '');
        $this->RegisterAttributeBoolean('VALVE_WATERCONTROL_ERRORCODE_enabled', false);
        $this->RegisterAttributeInteger('VALVE_WATERCONTROL_ERRORCODE_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_WATERCONTROL_ERRORCODE_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('VALVE_1_NAME', 'valve 1');
        $this->RegisterAttributeString('VALVE_2_NAME', 'valve 2');
        $this->RegisterAttributeString('VALVE_3_NAME', 'valve 3');
        $this->RegisterAttributeString('VALVE_4_NAME', 'valve 4');
        $this->RegisterAttributeString('VALVE_5_NAME', 'valve 5');
        $this->RegisterAttributeString('VALVE_6_NAME', 'valve 6');
        $this->RegisterAttributeString('VALVE_1_ACTIVITY', '');
        $this->RegisterAttributeBoolean('VALVE_1_ACTIVITY_enabled', false);
        $this->RegisterAttributeString('VALVE_2_ACTIVITY', '');
        $this->RegisterAttributeBoolean('VALVE_2_ACTIVITY_enabled', false);
        $this->RegisterAttributeString('VALVE_3_ACTIVITY', '');
        $this->RegisterAttributeBoolean('VALVE_3_ACTIVITY_enabled', false);
        $this->RegisterAttributeString('VALVE_4_ACTIVITY', '');
        $this->RegisterAttributeBoolean('VALVE_4_ACTIVITY_enabled', false);
        $this->RegisterAttributeString('VALVE_5_ACTIVITY', '');
        $this->RegisterAttributeBoolean('VALVE_5_ACTIVITY_enabled', false);
        $this->RegisterAttributeString('VALVE_6_ACTIVITY', '');
        $this->RegisterAttributeBoolean('VALVE_6_ACTIVITY_enabled', false);
        $this->RegisterAttributeBoolean('VALVE_1_ACTIVITY_STATE', false);
        $this->RegisterAttributeBoolean('VALVE_1_ACTIVITY_STATE_enabled', false);
        $this->RegisterAttributeBoolean('VALVE_2_ACTIVITY_STATE', false);
        $this->RegisterAttributeBoolean('VALVE_2_ACTIVITY_STATE_enabled', false);
        $this->RegisterAttributeBoolean('VALVE_3_ACTIVITY_STATE', false);
        $this->RegisterAttributeBoolean('VALVE_3_ACTIVITY_STATE_enabled', false);
        $this->RegisterAttributeBoolean('VALVE_4_ACTIVITY_STATE', false);
        $this->RegisterAttributeBoolean('VALVE_4_ACTIVITY_STATE_enabled', false);
        $this->RegisterAttributeBoolean('VALVE_5_ACTIVITY_STATE', false);
        $this->RegisterAttributeBoolean('VALVE_5_ACTIVITY_STATE_enabled', false);
        $this->RegisterAttributeBoolean('VALVE_6_ACTIVITY_STATE', false);
        $this->RegisterAttributeBoolean('VALVE_6_ACTIVITY_STATE_enabled', false);
        $this->RegisterAttributeInteger('VALVE_1_ACTIVITY_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_1_ACTIVITY_TIMESTAMP_enabled', false);
        $this->RegisterAttributeInteger('VALVE_2_ACTIVITY_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_2_ACTIVITY_TIMESTAMP_enabled', false);
        $this->RegisterAttributeInteger('VALVE_3_ACTIVITY_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_3_ACTIVITY_TIMESTAMP_enabled', false);
        $this->RegisterAttributeInteger('VALVE_4_ACTIVITY_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_4_ACTIVITY_TIMESTAMP_enabled', false);
        $this->RegisterAttributeInteger('VALVE_5_ACTIVITY_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_5_ACTIVITY_TIMESTAMP_enabled', false);
        $this->RegisterAttributeInteger('VALVE_6_ACTIVITY_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_6_ACTIVITY_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('VALVE_1_STATE', '');
        $this->RegisterAttributeBoolean('VALVE_1_STATE_enabled', false);
        $this->RegisterAttributeInteger('VALVE_1_STATE_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_1_STATE_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('VALVE_2_STATE', '');
        $this->RegisterAttributeBoolean('VALVE_2_STATE_enabled', false);
        $this->RegisterAttributeInteger('VALVE_2_STATE_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_2_STATE_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('VALVE_3_STATE', '');
        $this->RegisterAttributeBoolean('VALVE_3_STATE_enabled', false);
        $this->RegisterAttributeInteger('VALVE_3_STATE_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_3_STATE_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('VALVE_4_STATE', '');
        $this->RegisterAttributeBoolean('VALVE_4_STATE_enabled', false);
        $this->RegisterAttributeInteger('VALVE_4_STATE_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_4_STATE_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('VALVE_5_STATE', '');
        $this->RegisterAttributeBoolean('VALVE_5_STATE_enabled', false);
        $this->RegisterAttributeInteger('VALVE_5_STATE_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_5_STATE_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('VALVE_6_STATE', '');
        $this->RegisterAttributeBoolean('VALVE_6_STATE_enabled', false);
        $this->RegisterAttributeInteger('VALVE_6_STATE_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_6_STATE_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('VALVE_1_ERRORCODE', '');
        $this->RegisterAttributeBoolean('VALVE_1_ERRORCODE_enabled', false);
        $this->RegisterAttributeInteger('VALVE_1_ERRORCODE_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_1_ERRORCODE_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('VALVE_2_ERRORCODE', '');
        $this->RegisterAttributeBoolean('VALVE_2_ERRORCODE_enabled', false);
        $this->RegisterAttributeInteger('VALVE_2_ERRORCODE_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_2_ERRORCODE_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('VALVE_3_ERRORCODE', '');
        $this->RegisterAttributeBoolean('VALVE_3_ERRORCODE_enabled', false);
        $this->RegisterAttributeInteger('VALVE_3_ERRORCODE_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_3_ERRORCODE_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('VALVE_4_ERRORCODE', '');
        $this->RegisterAttributeBoolean('VALVE_4_ERRORCODE_enabled', false);
        $this->RegisterAttributeInteger('VALVE_4_ERRORCODE_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_4_ERRORCODE_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('VALVE_5_ERRORCODE', '');
        $this->RegisterAttributeBoolean('VALVE_5_ERRORCODE_enabled', false);
        $this->RegisterAttributeInteger('VALVE_5_ERRORCODE_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_5_ERRORCODE_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('VALVE_6_ERRORCODE', '');
        $this->RegisterAttributeBoolean('VALVE_6_ERRORCODE_enabled', false);
        $this->RegisterAttributeInteger('VALVE_6_ERRORCODE_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('VALVE_6_ERRORCODE_TIMESTAMP_enabled', false);
        $this->RegisterAttributeInteger('BATTERY_LEVEL', 0);
        $this->RegisterAttributeInteger('BATTERY_LEVEL_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('BATTERY_LEVEL_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('BATTERY_STATE', '');
        $this->RegisterAttributeInteger('BATTERY_STATE_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('BATTERY_STATE_TIMESTAMP_enabled', false);
        $this->RegisterAttributeInteger('RF_LINK_LEVEL', 0);
        $this->RegisterAttributeInteger('RF_LINK_LEVEL_TIMESTAMP', 0);
        $this->RegisterAttributeBoolean('RF_LINK_LEVEL_TIMESTAMP_enabled', false);
        $this->RegisterAttributeString('RF_LINK_STATE', '');
        $this->RegisterAttributeBoolean('RF_LINK_STATE_enabled', false);
        $this->RegisterAttributeInteger('soil_humidity', 0);
        $this->RegisterAttributeInteger('soil_humidity_timestamp', 0);
        $this->RegisterAttributeBoolean('soil_humidity_timestamp_enabled', false);
        $this->RegisterAttributeFloat('soil_temperature', 0);
        $this->RegisterAttributeInteger('soil_temperature_timestamp', 0);
        $this->RegisterAttributeBoolean('soil_temperature_timestamp_enabled', false);
        $this->RegisterAttributeFloat('ambient_temperature', 0);
        $this->RegisterAttributeInteger('ambient_temperature_timestamp', 0);
        $this->RegisterAttributeBoolean('ambient_temperature_timestamp_enabled', false);
        $this->RegisterAttributeInteger('light_intensity', 0);
        $this->RegisterAttributeInteger('light_intensity_timestamp', 0);
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
        $id = $this->ReadPropertyString('id');
        $this->SetReceiveDataFilter(".*" . $id . ".*");
        $this->ValidateConfiguration();
    }

    private function ValidateConfiguration()
    {
        $id = $this->ReadPropertyString('id');
        if ($id == '') {
            $this->SetStatus(205);
        } elseif ($id != '') {
            $this->RegisterVariables();
            $this->SetStatus(IS_ACTIVE);
        }
    }

    private function CheckRequest()
    {
        $id = $this->ReadPropertyString('id');
        $data = false;
        if ($id == '') {
            $this->SetStatus(205);
        } elseif ($id != '') {
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
        $reachable_ass = [
            [true, $this->Translate("Online"), "", -1],
            [false, $this->Translate("Offline"), "", -1]];
        $this->RegisterProfileAssociation('Gardena.Reachable', 'Network', '', '', 0, 1, 0, 0, VARIABLETYPE_BOOLEAN, $reachable_ass);

        $this->SetupVariable(
            'NAME', $this->Translate('name'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
        );
        $this->SetupVariable(
            'SERIAL', $this->Translate('serial'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
        );
        $model_type = $this->ReadPropertyString('model_type');
        $this->GetDeviceStatus();
        if ($model_type == self::GARDENA_smart_Irrigation_Control) {
            $valve_1_name = $this->ReadAttributeString('VALVE_1_NAME');
            $valve_1_state = $this->ReadAttributeString('VALVE_1_STATE');
            if($valve_1_state == 'AVAILABLE' || $valve_1_state == 'OK')
            {
                $this->SetupVariable(
                    'VALVE_1_ACTIVITY_STATE', $this->Translate($valve_1_name), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, true
                );
                $this->WriteAttributeBoolean('VALVE_1_ACTIVITY_STATE_enabled', true);
                $this->SetupVariable(
                    'VALVE_1_ERRORCODE', $valve_1_name . " " . $this->Translate('error code'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, true
                );
            }
            else
            {
                $this->SetupVariable(
                    'VALVE_1_ACTIVITY_STATE', $this->Translate($valve_1_name), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
                );
                $this->SetupVariable(
                    'VALVE_1_ERRORCODE', $valve_1_name . " " . $this->Translate('error code'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
                );
            }
            $valve_2_name = $this->ReadAttributeString('VALVE_2_NAME');
            $valve_2_state = $this->ReadAttributeString('VALVE_2_STATE');
            if($valve_2_state == 'AVAILABLE' || $valve_2_state == 'OK')
            {
                $this->SetupVariable(
                    'VALVE_2_ACTIVITY_STATE', $this->Translate($valve_2_name), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, true
                );
                $this->WriteAttributeBoolean('VALVE_2_ACTIVITY_STATE_enabled', true);
                $this->SetupVariable(
                    'VALVE_2_ERRORCODE', $valve_2_name . " " . $this->Translate('error code'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, true
                );
            }
            else
            {
                $this->SetupVariable(
                    'VALVE_2_ACTIVITY_STATE', $this->Translate($valve_2_name), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
                );
                $this->SetupVariable(
                    'VALVE_2_ERRORCODE', $valve_2_name . " " . $this->Translate('error code'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
                );
            }
            $valve_3_name = $this->ReadAttributeString('VALVE_3_NAME');
            $valve_3_state = $this->ReadAttributeString('VALVE_3_STATE');
            if($valve_3_state == 'AVAILABLE' || $valve_3_state == 'OK')
            {
                $this->SetupVariable(
                    'VALVE_3_ACTIVITY_STATE', $this->Translate($valve_3_name), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, true
                );
                $this->WriteAttributeBoolean('VALVE_3_ACTIVITY_STATE_enabled', true);
                $this->SetupVariable(
                    'VALVE_3_ERRORCODE', $valve_3_name . " " . $this->Translate('error code'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, true
                );
            }
            else
            {
                $this->SetupVariable(
                    'VALVE_3_ACTIVITY_STATE', $this->Translate($valve_3_name), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
                );
                $this->SetupVariable(
                    'VALVE_3_ERRORCODE', $valve_3_name . " " . $this->Translate('error code'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
                );
            }
            $valve_4_name = $this->ReadAttributeString('VALVE_4_NAME');
            $valve_4_state = $this->ReadAttributeString('VALVE_4_STATE');
            if($valve_4_state == 'AVAILABLE' || $valve_4_state == 'OK')
            {
                $this->SetupVariable(
                    'VALVE_4_ACTIVITY_STATE', $this->Translate($valve_4_name), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, true
                );
                $this->WriteAttributeBoolean('VALVE_4_ACTIVITY_STATE_enabled', true);
                $this->SetupVariable(
                    'VALVE_4_ERRORCODE', $valve_4_name . " " . $this->Translate('error code'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, true
                );
            }
            else
            {
                $this->SetupVariable(
                    'VALVE_4_ACTIVITY_STATE', $this->Translate($valve_4_name), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
                );
                $this->SetupVariable(
                    'VALVE_4_ERRORCODE', $valve_4_name . " " . $this->Translate('error code'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
                );
            }
            $valve_5_name = $this->ReadAttributeString('VALVE_5_NAME');
            $valve_5_state = $this->ReadAttributeString('VALVE_5_STATE');
            if($valve_5_state == 'AVAILABLE' || $valve_5_state == 'OK')
            {
                $this->SetupVariable(
                    'VALVE_5_ACTIVITY_STATE', $this->Translate($valve_5_name), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, true
                );
                $this->WriteAttributeBoolean('VALVE_5_ACTIVITY_STATE_enabled', true);
                $this->SetupVariable(
                    'VALVE_5_ERRORCODE', $valve_5_name . " " . $this->Translate('error code'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, true
                );
            }
            else
            {
                $this->SetupVariable(
                    'VALVE_5_ACTIVITY_STATE', $this->Translate($valve_5_name), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
                );
                $this->SetupVariable(
                    'VALVE_5_ERRORCODE', $valve_5_name . " " . $this->Translate('error code'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
                );
            }
            $valve_6_name = $this->ReadAttributeString('VALVE_6_NAME');
            $valve_6_state = $this->ReadAttributeString('VALVE_6_STATE');
            if($valve_6_state == 'AVAILABLE' || $valve_6_state == 'OK')
            {
                $this->SetupVariable(
                    'VALVE_6_ACTIVITY_STATE', $this->Translate($valve_6_name), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, true
                );
                $this->WriteAttributeBoolean('VALVE_6_ACTIVITY_STATE_enabled', true);
                $this->SetupVariable(
                    'VALVE_6_ERRORCODE', $valve_6_name . " " . $this->Translate('error code'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, true
                );
            }
            else
            {
                $this->SetupVariable(
                    'VALVE_6_ACTIVITY_STATE', $this->Translate($valve_6_name), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
                );
                $this->SetupVariable(
                    'VALVE_6_ERRORCODE', $valve_6_name . " " . $this->Translate('error code'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
                );
            }

            $this->SetupVariable(
                'RF_LINK_STATE', $this->Translate('rf link state'), 'Gardena.Reachable', $this->_getPosition(), VARIABLETYPE_BOOLEAN, false, false
            );
        }

        if ($model_type == self::GARDENA_smart_Water_Control) {
            $this->SetupVariable(
                'VALVE_WATERCONTROL_ACTIVITY_STATE', $this->Translate('activity'), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, false, true
            );
            $this->SetupVariable(
                'VALVE_WATERCONTROL_ACTIVITY_TIMESTAMP', $this->Translate('activity timestamp'), '~UnixTimestamp', $this->_getPosition(), VARIABLETYPE_INTEGER, false, false
            );

            $this->SetupVariable(
                'VALVE_WATERCONTROL_STATE', $this->Translate('state'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, true
            );
            $this->SetupVariable(
                'VALVE_WATERCONTROL_STATE_TIMESTAMP', $this->Translate('state timestamp'), '~UnixTimestamp', $this->_getPosition(), VARIABLETYPE_INTEGER, false, false
            );

            $this->SetupVariable(
                'VALVE_WATERCONTROL_ERRORCODE', $this->Translate('error code'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, true
            );
            $this->SetupVariable(
                'VALVE_WATERCONTROL_ERRORCODE_TIMESTAMP', $this->Translate('error code timestamp'), '~UnixTimestamp', $this->_getPosition(), VARIABLETYPE_INTEGER, false, false
            );

            $this->SetupVariable(
                'BATTERY_LEVEL', $this->Translate('battery level'), '~Battery.100', $this->_getPosition(), VARIABLETYPE_INTEGER, false, true
            );
            $this->SetupVariable(
                'BATTERY_LEVEL_TIMESTAMP', $this->Translate('battery level timestamp'), '~UnixTimestamp', $this->_getPosition(), VARIABLETYPE_INTEGER, false, false
            );

            $this->SetupVariable(
                'BATTERY_STATE', $this->Translate('battery state'), '~Battery.Reversed', $this->_getPosition(), VARIABLETYPE_BOOLEAN, false, true
            );
            $this->SetupVariable(
                'BATTERY_STATE_TIMESTAMP', $this->Translate('battery state timestamp'), '~UnixTimestamp', $this->_getPosition(), VARIABLETYPE_INTEGER, false, false
            );

            $this->SetupVariable(
                'RF_LINK_LEVEL', $this->Translate('rf link level'), '~Intensity.100', $this->_getPosition(), VARIABLETYPE_INTEGER, false, true
            );
            $this->SetupVariable(
                'RF_LINK_LEVEL_TIMESTAMP', $this->Translate('rf link level timestamp'), '~UnixTimestamp', $this->_getPosition(), VARIABLETYPE_INTEGER, false, false
            );
            $this->SetupVariable(
                'RF_LINK_STATE', $this->Translate('rf link state'), 'Gardena.Reachable', $this->_getPosition(), VARIABLETYPE_BOOLEAN, false, false
            );
        }

        if ($model_type == self::GARDENA_smart_Sensor) {
            $this->SetupVariable(
                'BATTERY_LEVEL', $this->Translate('battery level'), '~Battery.100', $this->_getPosition(), VARIABLETYPE_INTEGER, false, true
            );
            $this->SetupVariable(
                'BATTERY_LEVEL_TIMESTAMP', $this->Translate('battery level timestamp'), '~UnixTimestamp', $this->_getPosition(), VARIABLETYPE_INTEGER, false, false
            );

            $this->SetupVariable(
                'BATTERY_STATE', $this->Translate('battery state'), '~Battery.Reversed', $this->_getPosition(), VARIABLETYPE_BOOLEAN, false, true
            );
            $this->SetupVariable(
                'BATTERY_STATE_TIMESTAMP', $this->Translate('battery state timestamp'), '~UnixTimestamp', $this->_getPosition(), VARIABLETYPE_INTEGER, false, false
            );

            $this->SetupVariable(
                'RF_LINK_LEVEL', $this->Translate('rf link level'), '~Intensity.100', $this->_getPosition(), VARIABLETYPE_INTEGER, false, true
            );
            $this->SetupVariable(
                'RF_LINK_LEVEL_TIMESTAMP', $this->Translate('rf link level timestamp'), '~UnixTimestamp', $this->_getPosition(), VARIABLETYPE_INTEGER, false, false
            );
            $this->SetupVariable(
                'RF_LINK_STATE', $this->Translate('rf link state'), 'Gardena.Reachable', $this->_getPosition(), VARIABLETYPE_BOOLEAN, false, false
            );

            $this->SetupVariable(
                'soil_humidity', $this->Translate('soil humidity'), '~Humidity', $this->_getPosition(), VARIABLETYPE_INTEGER, false, true
            );
            $this->SetupVariable(
                'soil_humidity_timestamp', $this->Translate('soil humidity timestamp'), '~UnixTimestamp', $this->_getPosition(), VARIABLETYPE_INTEGER, false, false
            );

            $this->SetupVariable(
                'soil_temperature', $this->Translate('soil temperature'), '~Temperature', $this->_getPosition(), VARIABLETYPE_FLOAT, false, true
            );
            $this->SetupVariable(
                'soil_temperature_timestamp', $this->Translate('soil temperature timestamp'), '~UnixTimestamp', $this->_getPosition(), VARIABLETYPE_INTEGER, false, false
            );

            $this->SetupVariable(
                'ambient_temperature', $this->Translate('ambient temperature'), '~Temperature', $this->_getPosition(), VARIABLETYPE_FLOAT, false, true
            );
            $this->SetupVariable(
                'ambient_temperature_timestamp', $this->Translate('ambient temperature timestamp'), '~UnixTimestamp', $this->_getPosition(), VARIABLETYPE_INTEGER, false, false
            );

            $this->SetupVariable(
                'light_intensity', $this->Translate('light intensity'), '~Illumination', $this->_getPosition(), VARIABLETYPE_INTEGER, false, true
            );
            $this->SetupVariable(
                'light_intensity_timestamp', $this->Translate('light intensity timestamp'), '~UnixTimestamp', $this->_getPosition(), VARIABLETYPE_INTEGER, false, false
            );
        }
        // $this->WriteValues();


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
            if ($ident == 'NAME' || $ident == 'SERIAL') {
                $ident = strtolower($ident);
            }
            $visible = $this->ReadAttributeBoolean($ident . '_enabled');
            $this->SendDebug('Gardena Variable:', 'Variable with Ident ' . $ident . ' is shown ' . print_r($visible, true), 0);
        }
        if ($visible == true) {
            switch ($vartype) {
                case VARIABLETYPE_BOOLEAN:
                    $objid = $this->RegisterVariableBoolean($ident, $name, $profile, $position);
                    if ($ident == 'BATTERY_STATE') {
                        $string_value = $this->ReadAttributeString($ident);
                        if ($string_value == 'OK') {
                            $value = true;
                        } else {
                            $value = false;
                        }
                    }elseif($ident == 'RF_LINK_STATE')
                    {
                        $string_value = $this->ReadAttributeString($ident);
                        if ($string_value == 'ONLINE') {
                            $value = true;
                        } else {
                            $value = false;
                        }
                    }
                    else {
                        $value = $this->ReadAttributeBoolean($ident);
                    }
                    break;
                case VARIABLETYPE_INTEGER:
                    $objid = $this->RegisterVariableInteger($ident, $name, $profile, $position);
                    $value = $this->ReadAttributeInteger($ident);
                    break;
                case VARIABLETYPE_FLOAT:
                    $objid = $this->RegisterVariableFloat($ident, $name, $profile, $position);
                    $value = $this->ReadAttributeFloat($ident);
                    break;
                case VARIABLETYPE_STRING:
                    $objid = $this->RegisterVariableString($ident, $name, $profile, $position);
                    if ($ident == 'name' || $ident == 'serial') {
                        $value = $this->ReadPropertyString($ident);
                    } else {
                        $value = $this->ReadAttributeString($ident);
                    }
                    break;
            }
            $this->SetValue($ident, $value);
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
        if ($Ident === 'VALVE_1_ACTIVITY_STATE') {
            $this->ToggleValve($Value, 1);
        }
        if ($Ident === 'VALVE_2_ACTIVITY_STATE') {
            $this->ToggleValve($Value, 2);
        }
        if ($Ident === 'VALVE_3_ACTIVITY_STATE') {
            $this->ToggleValve($Value, 3);
        }
        if ($Ident === 'VALVE_4_ACTIVITY_STATE') {
            $this->ToggleValve($Value, 4);
        }
        if ($Ident === 'VALVE_5_ACTIVITY_STATE') {
            $this->ToggleValve($Value, 5);
        }
        if ($Ident === 'VALVE_6_ACTIVITY_STATE') {
            $this->ToggleValve($Value, 6);
        }
        if ($Ident === 'VALVE_WATERCONTROL_ACTIVITY_STATE') {
            $this->ToggleValve($Value, 0);
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
            'id' => $service_id,
            'type' => 'WEBSOCKET',
            'attributes' => [
                'locationId' => $locationId
            ]
        ]];
        $data = json_encode($payload);
        $result = json_decode($this->SendDataToParent(json_encode([
            'DataID' => '{0FE98840-1BBA-4E87-897D-30506FEF540A}',
            'Type' => 'POST',
            'Endpoint' => '/websocket',
            'Payload' => $data
        ])));
        return $result;
    }

    /** Control behaviour of devices.
     * PUT
     */
    public function ControlDevice(string $service_id, string $type, string $command, string $parameter)
    {
        $payload = ['data' => [
            'id' => $service_id,
            'type' => $type,
            'attributes' => [
                'command' => $command,
                'seconds' => $parameter
            ]
        ]];
        $data = json_encode($payload);
        $this->SendCommand($service_id, $data);
    }

    public function ToggleValve(bool $state, int $index)
    {
        if ($state) {
            $this->OpenValve($index);
        } else {
            $this->StopValve($index);
        }
    }

    /** START
     * manual operation, use 'seconds' attribute to define
     */
    public function OpenValve(int $index)
    {
        $id = $this->GetValveID($index);
        $this->ControlDevice($id, 'VALVE_CONTROL', 'START_SECONDS_TO_OVERRIDE', $this->GetWateringInterval());
    }

    private function GetWateringInterval()
    {
        // TODO add interval slider
        $seconds = strval(3600);
        return $seconds;
    }

    public function StopValve(int $index)
    {
        $id = $this->GetValveID($index);
        $this->ControlDevice($id, 'VALVE_CONTROL', 'STOP_UNTIL_NEXT_TASK', "0");
    }

    public function PauseValve(int $index)
    {
        $id = $this->GetValveID($index);
        $this->ControlDevice($id, 'VALVE_CONTROL', 'PAUSE', "0");
    }

    public function UnpauseValve(int $index)
    {
        $id = $this->GetValveID($index);
        $this->ControlDevice($id, 'VALVE_CONTROL', 'UNPAUSE', "0");
    }

    private function GetValveID($index)
    {
        if($index == 0)
        {
            $valve_id = $this->ReadAttributeString('id');
        }
        else{
            $valve_id = $this->ReadAttributeString('id') . ':' . $index;
        }
        return $valve_id;
    }


    private function GetValveData($device)
    {
        $name = $device['attributes']['name']['value'];
        $valve_id = explode(':', $device['id']);
        $id = $valve_id[0];
        if (isset($valve_id[1])) {
            $valve_key = $valve_id[1];
        } else {
            $valve_key = 'WATERCONTROL';
        }

        $instance_id = $this->ReadPropertyString('id');
        if ($instance_id == $id) {
            $this->SendDebug('Gardena Valve ' . $id, $name, 0);
            $this->WriteAttributeString('VALVE_' . $valve_key . '_NAME', $name);
            if($valve_key == 'WATERCONTROL')
            {
                if (isset($device['attributes']['activity']['value'])) {
                    $activity = $device['attributes']['activity']['value'];
                    $this->WriteAttributeString('VALVE_WATERCONTROL_ACTIVITY', $activity);
                    $activity_timestamp = $device['attributes']['activity']['timestamp'];
                    $this->WriteAttributeInteger('VALVE_WATERCONTROL_ACTIVITY_TIMESTAMP', $this->CalculateTime($activity_timestamp, 'Device ' . $name . ' activity'));
                }
                if (isset($device['attributes']['state']['value'])) {
                    $state = $device['attributes']['state']['value'];
                    $this->WriteAttributeString('VALVE_WATERCONTROL_STATE', $state);
                    $state_timestamp = $device['attributes']['state']['timestamp'];
                    $this->WriteAttributeInteger('VALVE_WATERCONTROL_STATE_TIMESTAMP', $this->CalculateTime($state_timestamp, 'Device ' . $name . ' state'));
                }
                if (isset($device['attributes']['lastErrorCode']['value'])) {
                    $lastErrorCode = $device['attributes']['lastErrorCode']['value'];
                    $this->WriteAttributeString('VALVE_WATERCONTROL_ERRORCODE', $lastErrorCode);
                    $lastErrorCode_timestamp = $device['attributes']['lastErrorCode']['timestamp'];
                    $this->WriteAttributeInteger('VALVE_WATERCONTROL_ERRORCODE_TIMESTAMP', $this->CalculateTime($lastErrorCode_timestamp, 'Device ' . $name . ' last error code'));
                }
            }
            else{
                if (isset($device['attributes']['activity']['value'])) {
                    $activity = $device['attributes']['activity']['value'];
                    $this->WriteAttributeString('VALVE_' . $valve_key . '_ACTIVITY', $activity);
                    if($activity == 'OPEN')
                    {
                        $this->WriteAttributeBoolean('VALVE_' . $valve_key . '_ACTIVITY_STATE', true);
                    }
                    elseif($activity == 'CLOSED')
                    {
                        $this->WriteAttributeBoolean('VALVE_' . $valve_key . '_ACTIVITY_STATE', false);
                    }
                    $activity_timestamp = $device['attributes']['activity']['timestamp'];
                    $this->WriteAttributeInteger('VALVE_' . $valve_key . '_ACTIVITY_TIMESTAMP', $this->CalculateTime($activity_timestamp, 'Device ' . $name . ' activity'));
                }
                if (isset($device['attributes']['state']['value'])) {
                    $state = $device['attributes']['state']['value'];
                    $this->WriteAttributeString('VALVE_' . $valve_key . '_STATE', $state);
                    $state_timestamp = $device['attributes']['state']['timestamp'];
                    $this->WriteAttributeInteger('VALVE_' . $valve_key . '_STATE_TIMESTAMP', $this->CalculateTime($state_timestamp, 'Device ' . $name . ' state'));
                }
                if (isset($device['attributes']['lastErrorCode']['value'])) {
                    $lastErrorCode = $device['attributes']['lastErrorCode']['value'];
                    $this->WriteAttributeString('VALVE_' . $valve_key . '_ERRORCODE', $lastErrorCode);
                    $lastErrorCode_timestamp = $device['attributes']['lastErrorCode']['timestamp'];
                    $this->WriteAttributeInteger('VALVE_' . $valve_key . '_ERRORCODE_TIMESTAMP', $this->CalculateTime($lastErrorCode_timestamp, 'Device ' . $name . ' last error code'));
                }
            }

        }
        return $name;
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
        if ($model_type == $model_type_instance && $id == $id_instance) {
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
        $this->WriteAttributeInteger('BATTERY_LEVEL_TIMESTAMP', $this->CalculateTime($battery_level_timestamp, 'Device ' . $name . ' battery level'));
        $battery_state = $device['attributes']['batteryState']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'battery state: ' . $battery_state, 0);
        $this->WriteAttributeString('BATTERY_STATE', $battery_state);
        $battery_state_timestamp = $device['attributes']['batteryState']['timestamp'];
        $this->WriteAttributeInteger('BATTERY_STATE_TIMESTAMP', $this->CalculateTime($battery_state_timestamp, 'Device ' . $name . ' battery state'));
        $rf_link_level = $device['attributes']['rfLinkLevel']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'RF link level: ' . $rf_link_level . '%', 0);
        $this->WriteAttributeInteger('RF_LINK_LEVEL', $rf_link_level);
        $rf_link_level_timestamp = $device['attributes']['rfLinkLevel']['timestamp'];
        $this->WriteAttributeInteger('RF_LINK_LEVEL_TIMESTAMP', $this->CalculateTime($rf_link_level_timestamp, 'Device ' . $name . ' RF link level'));
        $serial = $device['attributes']['serial']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'serial: ' . $serial, 0);
        $rf_link_state = $device['attributes']['rfLinkState']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'RF link state: ' . $rf_link_state, 0);
        $this->WriteAttributeString('RF_LINK_STATE', $rf_link_state);

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
        $this->WriteAttributeString('RF_LINK_STATE', $rf_link_state);
        return ['id' => $id, 'name' => $name, 'serial' => $serial, 'rf_link_state' => $rf_link_state];
    }

    private function GetSensorData($device)
    {
        $soil_humidity = $device['attributes']['soilHumidity']['value'];
        $this->SendDebug('Gardena Sensor Humidity', $soil_humidity . ' %', 0);
        $this->WriteAttributeInteger('soil_humidity', $soil_humidity);
        $soil_humidity_timestamp = $device['attributes']['soilHumidity']['timestamp'];
        $this->WriteAttributeInteger('soil_humidity_timestamp', $this->CalculateTime($soil_humidity_timestamp, 'Sensor Humidity'));
        $soil_temperature = $device['attributes']['soilTemperature']['value'];
        $this->SendDebug('Gardena Sensor Temperature', $soil_temperature . ' °C', 0);
        $this->WriteAttributeFloat('soil_temperature', $soil_temperature);
        $soil_temperature_timestamp = $device['attributes']['soilTemperature']['timestamp'];
        $this->WriteAttributeInteger('soil_temperature_timestamp', $this->CalculateTime($soil_temperature_timestamp, 'Sensor Temperature'));
        $ambient_temperature = $device['attributes']['ambientTemperature']['value'];
        $this->SendDebug('Gardena Sensor Ambient Temperature', $ambient_temperature . ' °C', 0);
        $this->WriteAttributeFloat('ambient_temperature', $ambient_temperature);
        $ambient_temperature_timestamp = $device['attributes']['ambientTemperature']['timestamp'];
        $this->WriteAttributeInteger('ambient_temperature_timestamp', $this->CalculateTime($ambient_temperature_timestamp, 'Sensor Ambient Temperature'));
        $light_intensity = $device['attributes']['lightIntensity']['value'];
        $this->SendDebug('Gardena Sensor Light Intensity', $light_intensity . ' lx', 0);
        $this->WriteAttributeInteger('light_intensity', $light_intensity);
        $light_intensity_timestamp = $device['attributes']['lightIntensity']['timestamp'];
        $this->WriteAttributeInteger('light_intensity_timestamp', $this->CalculateTime($light_intensity_timestamp, 'Sensor Light Intensity'));
    }

    private function CalculateTime($time_string, $subject)
    {
        $date = new DateTime($time_string);
        $date->setTimezone(new DateTimeZone('Europe/Berlin'));
        $timestamp = $date->getTimestamp();
        $this->SendDebug('Gardena ' . $subject . ' Timestamp', $date->format('Y-m-d H:i:sP'), 0);
        return $timestamp;
    }

    private function GetDeviceStatus()
    {
        $snapshot = $this->RequestStatus('snapshot');
        if ($snapshot != '[]') {
            $this->CheckDeviceData($snapshot);
        }
    }

    private function CheckDeviceData($snapshot)
    {
        $payload = json_decode($snapshot, true);
        if (!empty($snapshot)) {
            // check snapshot
            if(isset($payload['included']))
            {
                $included = $payload['included'];
                foreach ($included as $device) {
                    $type = $device['type'];
                    if ($type == 'VALVE') {
                        $this->GetValveData($device);
                    }
                    if ($type == 'COMMON') {
                        $this->GetDeviceData($device);
                    }
                    if ($type == 'SENSOR') {
                        $this->GetSensorData($device);
                    }
                }
                $this->WriteValues();
            }
            // check websocket response
            if(isset($payload['type']))
            {
                $type = $payload['type'];
                $this->SendDebug('Websocket Data', 'Type: ' . $type, 0);
                if ($type == 'VALVE') {
                    $this->SendDebug('Websocket Data', 'GetValveData', 0);
                    $this->GetValveData($payload);
                }
                if ($type == 'COMMON') {
                    $this->SendDebug('Websocket Data', 'GetDeviceData', 0);
                    $this->GetDeviceData($payload);
                }
                if ($type == 'SENSOR') {
                    $this->SendDebug('Websocket Data', 'GetSensorData', 0);
                    $this->GetSensorData($payload);
                }
                $this->WriteValues();
            }
        }
    }

    private function WriteEnabledValue($ident, $vartype, $enabled = false)
    {
        if ($enabled) {
            $value_enabled = true;
        } else {
            $value_enabled = $this->ReadAttributeBoolean($ident . '_enabled');
        }

        if ($value_enabled) {
            switch ($vartype) {
                case VARIABLETYPE_BOOLEAN:
                    if ($ident == 'BATTERY_STATE' || $ident == 'RF_LINK_STATE') {
                        $string_value = $this->ReadAttributeString($ident);
                        if ($string_value == 'OK' || $string_value == 'ONLINE') {
                            $value = true;
                            $debug_value = 'true';
                        } else {
                            $value = false;
                            $debug_value = 'false';
                        }
                    }
                    else {
                        $value = $this->ReadAttributeBoolean($ident);
                        $debug_value = strval($value);
                    }
                    $this->SendDebug('SetValue boolean', 'ident: ' . $ident . ' value: ' . $debug_value, 0);
                    $this->SetVariableValue($ident, $value);
                    break;
                case VARIABLETYPE_INTEGER:
                    $value = $this->ReadAttributeInteger($ident);
                    $this->SendDebug('SetValue integer', 'ident: ' . $ident . ' value: ' . $value, 0);
                    $this->SetVariableValue($ident, $value);
                    break;
                case VARIABLETYPE_FLOAT:
                    $value = $this->ReadAttributeFloat($ident);
                    $this->SendDebug('SetValue float', 'ident: ' . $ident . ' value: ' . $value, 0);
                    $this->SetVariableValue($ident, $value);
                    break;
                case VARIABLETYPE_STRING:
                    $value = $this->ReadAttributeString($ident);
                    $this->SendDebug('SetValue string', 'ident: ' . $ident . ' value: ' . $value, 0);
                    $this->SetVariableValue($ident, $value);
                    break;
            }
        }
    }

    private function SetVariableValue($ident, $value)
    {
        if(@$this->GetIDForIdent($ident))
        {
            $this->SetValue($ident, $value);
        }
    }

    private function WriteValues()
    {
        $model_type_instance = $this->ReadPropertyString('model_type');
        if ($model_type_instance == self::GARDENA_smart_Irrigation_Control) {
            $this->SendDebug('Gardena Request Response', self::GARDENA_smart_Irrigation_Control, 0);
            $this->WriteEnabledValue('VALVE_1_ACTIVITY_STATE', VARIABLETYPE_BOOLEAN);
            $this->WriteEnabledValue('VALVE_2_ACTIVITY_STATE', VARIABLETYPE_BOOLEAN);
            $this->WriteEnabledValue('VALVE_3_ACTIVITY_STATE', VARIABLETYPE_BOOLEAN);
            $this->WriteEnabledValue('VALVE_4_ACTIVITY_STATE', VARIABLETYPE_BOOLEAN);
            $this->WriteEnabledValue('VALVE_5_ACTIVITY_STATE', VARIABLETYPE_BOOLEAN);
            $this->WriteEnabledValue('VALVE_6_ACTIVITY_STATE', VARIABLETYPE_BOOLEAN);
            $this->WriteEnabledValue('VALVE_1_ERRORCODE', VARIABLETYPE_STRING);
            $this->WriteEnabledValue('VALVE_2_ERRORCODE', VARIABLETYPE_STRING);
            $this->WriteEnabledValue('VALVE_3_ERRORCODE', VARIABLETYPE_STRING);
            $this->WriteEnabledValue('VALVE_4_ERRORCODE', VARIABLETYPE_STRING);
            $this->WriteEnabledValue('VALVE_5_ERRORCODE', VARIABLETYPE_STRING);
            $this->WriteEnabledValue('VALVE_6_ERRORCODE', VARIABLETYPE_STRING);
            $this->WriteEnabledValue('RF_LINK_STATE', VARIABLETYPE_BOOLEAN);
        }

        if ($model_type_instance == self::GARDENA_smart_Water_Control) {
            $this->SendDebug('Gardena Request Response', self::GARDENA_smart_Water_Control, 0);
            $this->WriteEnabledValue('VALVE_WATERCONTROL_ACTIVITY_STATE', VARIABLETYPE_BOOLEAN);
            $this->WriteEnabledValue('VALVE_WATERCONTROL_ERRORCODE', VARIABLETYPE_STRING);
            $this->WriteEnabledValue('RF_LINK_STATE', VARIABLETYPE_BOOLEAN);
        }

        if ($model_type_instance == self::GARDENA_smart_Sensor) {
            $this->SendDebug('Gardena Write Values', self::GARDENA_smart_Sensor, 0);
            $this->WriteEnabledValue('BATTERY_LEVEL', VARIABLETYPE_INTEGER, true);
            $this->WriteEnabledValue('BATTERY_LEVEL_TIMESTAMP', VARIABLETYPE_INTEGER);
            $this->WriteEnabledValue('BATTERY_STATE', VARIABLETYPE_BOOLEAN, true);
            $this->WriteEnabledValue('BATTERY_STATE_TIMESTAMP', VARIABLETYPE_INTEGER);
            $this->WriteEnabledValue('RF_LINK_LEVEL', VARIABLETYPE_INTEGER, true);
            $this->WriteEnabledValue('RF_LINK_LEVEL_TIMESTAMP', VARIABLETYPE_INTEGER);
            $this->WriteEnabledValue('RF_LINK_STATE', VARIABLETYPE_BOOLEAN);
            $this->WriteEnabledValue('soil_humidity', VARIABLETYPE_INTEGER, true);
            $this->WriteEnabledValue('soil_humidity_timestamp', VARIABLETYPE_INTEGER);
            $this->WriteEnabledValue('soil_temperature', VARIABLETYPE_FLOAT, true);
            $this->WriteEnabledValue('soil_temperature_timestamp', VARIABLETYPE_INTEGER);
            $this->WriteEnabledValue('ambient_temperature', VARIABLETYPE_FLOAT, true);
            $this->WriteEnabledValue('ambient_temperature_timestamp', VARIABLETYPE_INTEGER);
            $this->WriteEnabledValue('light_intensity', VARIABLETYPE_INTEGER, true);
            $this->WriteEnabledValue('light_intensity_timestamp', VARIABLETYPE_INTEGER);
        }
    }

    public function RequestStatus(string $endpoint)
    {
        $data = $this->SendDataToParent(json_encode([
            'DataID' => '{0FE98840-1BBA-4E87-897D-30506FEF540A}',
            'Type' => 'GET',
            'Endpoint' => $endpoint,
            'Payload' => ''
        ]));
        $this->SendDebug('Gardena Request Response', $data, 0);
        return $data;
    }

    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString);
        $snapshot = $data->Buffer;
        $this->SendDebug('Receive Snapshot', $snapshot, 0);
        if ($snapshot != '[]') {
            $this->CheckDeviceData($snapshot);
        }
    }

    public function SendCommand(string $service_id, string $data)
    {
        $result = $this->SendDataToParent(json_encode([
            'DataID' => '{0FE98840-1BBA-4E87-897D-30506FEF540A}',
            'Type' => 'PUT',
            'Endpoint' => '/command/' . $service_id,
            'Payload' => $data
        ]));
        return $result;
    }

    public function SetWebFrontVariable(string $ident, bool $value)
    {
        $this->WriteAttributeBoolean($ident, $value);
        if ($value) {
            $this->SendDebug('Gardena Webfront Variable', $ident . ' enabled', 0);
        } else {
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
        if ($data != false) {
            $form = [
                [
                    'type'  => 'Image',
                    'image' => 'data:image/png;base64, ' . self::PICTURE_LOGO_GARDENA],
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
        } else {
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
        $form = [
            [
                'name' => 'name_enabled',
                'type' => 'CheckBox',
                'caption' => 'name',
                'visible' => true,
                'value' => $this->ReadAttributeBoolean('name_enabled'),
                'onChange' => 'Gardena_SetWebFrontVariable($id, "name_enabled", $name_enabled);'],
            [
                'name' => 'serial_enabled',
                'type' => 'CheckBox',
                'caption' => 'serial',
                'visible' => true,
                'value' => $this->ReadAttributeBoolean('serial_enabled'),
                'onChange' => 'Gardena_SetWebFrontVariable($id, "serial_enabled", $serial_enabled);']
        ];

        if ($model_type == self::GARDENA_smart_Irrigation_Control) {
            $form = array_merge_recursive(
                $form, [
                    [
                        'name' => 'VALVE_1_ACTIVITY_STATE_enabled',
                        'type' => 'CheckBox',
                        'caption' => 'valve 1',
                        'visible' => true,
                        'value' => $this->ReadAttributeBoolean('VALVE_1_ACTIVITY_STATE_enabled'),
                        'onChange' => 'Gardena_SetWebFrontVariable($id, "VALVE_1_ACTIVITY_STATE_enabled", $VALVE_1_ACTIVITY_STATE_enabled);'],
                    [
                        'name' => 'VALVE_2_ACTIVITY_STATE_enabled',
                        'type' => 'CheckBox',
                        'caption' => 'valve 2',
                        'visible' => true,
                        'value' => $this->ReadAttributeBoolean('VALVE_2_ACTIVITY_STATE_enabled'),
                        'onChange' => 'Gardena_SetWebFrontVariable($id, "VALVE_2_ACTIVITY_STATE_enabled", $VALVE_2_ACTIVITY_STATE_enabled);'],
                    [
                        'name' => 'VALVE_3_ACTIVITY_STATE_enabled',
                        'type' => 'CheckBox',
                        'caption' => 'valve 3',
                        'visible' => true,
                        'value' => $this->ReadAttributeBoolean('VALVE_3_ACTIVITY_STATE_enabled'),
                        'onChange' => 'Gardena_SetWebFrontVariable($id, "VALVE_3_ACTIVITY_STATE_enabled", $VALVE_3_ACTIVITY_STATE_enabled);'],
                    [
                        'name' => 'VALVE_4_ACTIVITY_STATE_enabled',
                        'type' => 'CheckBox',
                        'caption' => 'valve 4',
                        'visible' => true,
                        'value' => $this->ReadAttributeBoolean('VALVE_4_ACTIVITY_STATE_enabled'),
                        'onChange' => 'Gardena_SetWebFrontVariable($id, "VALVE_4_ACTIVITY_STATE_enabled", $VALVE_4_ACTIVITY_STATE_enabled);'],
                    [
                        'name' => 'VALVE_5_ACTIVITY_STATE_enabled',
                        'type' => 'CheckBox',
                        'caption' => 'valve 5',
                        'visible' => true,
                        'value' => $this->ReadAttributeBoolean('VALVE_5_ACTIVITY_STATE_enabled'),
                        'onChange' => 'Gardena_SetWebFrontVariable($id, "VALVE_5_ACTIVITY_STATE_enabled", $VALVE_5_ACTIVITY_STATE_enabled);'],
                    [
                        'name' => 'VALVE_6_ACTIVITY_STATE_enabled',
                        'type' => 'CheckBox',
                        'caption' => 'valve 6',
                        'visible' => true,
                        'value' => $this->ReadAttributeBoolean('VALVE_6_ACTIVITY_STATE_enabled'),
                        'onChange' => 'Gardena_SetWebFrontVariable($id, "VALVE_6_ACTIVITY_STATE_enabled", $VALVE_6_ACTIVITY_STATE_enabled);'],
                    [
                        'name' => 'RF_LINK_STATE_enabled',
                        'type' => 'CheckBox',
                        'caption' => 'rf link state',
                        'visible' => true,
                        'value' => $this->ReadAttributeBoolean('RF_LINK_STATE_enabled'),
                        'onChange' => 'Gardena_SetWebFrontVariable($id, "RF_LINK_STATE_enabled", $RF_LINK_STATE_enabled);']]
            );
        } elseif ($model_type == self::GARDENA_smart_Sensor) {
            $form = array_merge_recursive(
                $form, [
                    [
                        'name' => 'BATTERY_LEVEL_TIMESTAMP_enabled',
                        'type' => 'CheckBox',
                        'caption' => 'battery level timestamp',
                        'visible' => true,
                        'value' => $this->ReadAttributeBoolean('BATTERY_LEVEL_TIMESTAMP_enabled'),
                        'onChange' => 'Gardena_SetWebFrontVariable($id, "BATTERY_LEVEL_TIMESTAMP_enabled", $BATTERY_LEVEL_TIMESTAMP_enabled);'],
                    [
                        'name' => 'BATTERY_STATE_TIMESTAMP_enabled',
                        'type' => 'CheckBox',
                        'caption' => 'battery state timestamp',
                        'visible' => true,
                        'value' => $this->ReadAttributeBoolean('BATTERY_STATE_TIMESTAMP_enabled'),
                        'onChange' => 'Gardena_SetWebFrontVariable($id, "BATTERY_STATE_TIMESTAMP_enabled", $BATTERY_STATE_TIMESTAMP_enabled);'],
                    [
                        'name' => 'RF_LINK_STATE_enabled',
                        'type' => 'CheckBox',
                        'caption' => 'rf link state',
                        'visible' => true,
                        'value' => $this->ReadAttributeBoolean('RF_LINK_STATE_enabled'),
                        'onChange' => 'Gardena_SetWebFrontVariable($id, "RF_LINK_STATE_enabled", $RF_LINK_STATE_enabled);'],
                    [
                        'name' => 'RF_LINK_LEVEL_TIMESTAMP_enabled',
                        'type' => 'CheckBox',
                        'caption' => 'rf link level timestamp',
                        'visible' => true,
                        'value' => $this->ReadAttributeBoolean('RF_LINK_LEVEL_TIMESTAMP_enabled'),
                        'onChange' => 'Gardena_SetWebFrontVariable($id, "RF_LINK_LEVEL_TIMESTAMP_enabled", $RF_LINK_LEVEL_TIMESTAMP_enabled);'],
                    [
                        'name' => 'soil_humidity_timestamp_enabled',
                        'type' => 'CheckBox',
                        'caption' => 'soil humidity timestamp',
                        'visible' => true,
                        'value' => $this->ReadAttributeBoolean('soil_humidity_timestamp_enabled'),
                        'onChange' => 'Gardena_SetWebFrontVariable($id, "soil_humidity_timestamp_enabled", $soil_humidity_timestamp_enabled);'],
                    [
                        'name' => 'soil_temperature_timestamp_enabled',
                        'type' => 'CheckBox',
                        'caption' => 'soil temperature timestamp',
                        'visible' => true,
                        'value' => $this->ReadAttributeBoolean('soil_temperature_timestamp_enabled'),
                        'onChange' => 'Gardena_SetWebFrontVariable($id, "soil_temperature_timestamp_enabled", $soil_temperature_timestamp_enabled);'],
                    [
                        'name' => 'ambient_temperature_timestamp_enabled',
                        'type' => 'CheckBox',
                        'caption' => 'ambient temperature timestamp',
                        'visible' => true,
                        'value' => $this->ReadAttributeBoolean('ambient_temperature_timestamp_enabled'),
                        'onChange' => 'Gardena_SetWebFrontVariable($id, "ambient_temperature_timestamp_enabled", $ambient_temperature_timestamp_enabled);'],
                    [
                        'name' => 'light_intensity_timestamp_enabled',
                        'type' => 'CheckBox',
                        'caption' => 'light intensity timestamp',
                        'visible' => true,
                        'value' => $this->ReadAttributeBoolean('light_intensity_timestamp_enabled'),
                        'onChange' => 'Gardena_SetWebFrontVariable($id, "light_intensity_timestamp_enabled", $light_intensity_timestamp_enabled);']]
            );
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
