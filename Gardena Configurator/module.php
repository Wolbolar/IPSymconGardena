<?php
declare(strict_types=1);

class GardenaConfigurator extends IPSModule
{
    private const PICTURE_LOGO_GARDENA = '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';

    public function Create()
    {
        //Never delete this line!
        parent::Create();
        $this->ConnectParent('{9775D7CA-5667-8554-0172-2EBB2F553A54}');
        $this->RegisterPropertyInteger("ImportCategoryID", 0);
        $this->RegisterAttributeString('location_snapshot', '[]');
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        $import_category = $this->ReadPropertyInteger('ImportCategoryID');
        if($import_category == 0)
        {
            $this->SetStatus(202);
        }
        /*
        $token = $this->GetGardenaToken();
        if ($token == '') {
            $this->SendDebug('Gardena Token', $token, 0);
            $this->SendDebug('Gardena Token', 'Instance set inactive', 0);
            $this->SetStatus(IS_INACTIVE);
        } else {
            $this->SetStatus(IS_ACTIVE);
        }
        */
        $this->SetStatus(IS_ACTIVE);
    }

    public function GetGardenaToken()
    {
        $token = $this->RequestDataFromParent('token');
        return $token;
    }

    /** Get Snapshot
     * @return bool|false|string
     */
    public function RequestSnapshot()
    {
        $location_id = $this->RequestDataFromParent('location_id');
        if ($location_id != '') {
            $snapshot = $this->RequestDataFromParent('snapshotbuffer');
        } else {
            $snapshot = '[]';
        }
        $this->SendDebug('Gardena Request Response', $snapshot, 0);
        $this->WriteAttributeString('location_snapshot', $snapshot);
        return $snapshot;
    }

    /** Get Snapshot
     * @return bool|false|string
     */
    public function RequestSnapshotBuffer()
    {
        // $this->WriteAttributeString('location_snapshot', '[]');
        return $this->ReadAttributeString('location_snapshot');
    }

    /** Get Locations
     * @return bool|false|string
     */
    public function RequestLocations()
    {
        $location_id = $this->RequestDataFromParent('request_location_id');
        return $location_id;
    }

    public function GetConfiguration()
    {
        $location_id = $this->RequestDataFromParent('location_id');
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

    public function RequestDataFromParent(string $endpoint)
    {
        $data = $this->SendDataToParent(json_encode([
            'DataID'   => '{3F1DBEA8-38D6-12F4-A487-AF43F6326060}',
            'Type' => 'GET',
            'Endpoint' => $endpoint,
            'Payload'  => ''
        ]));
        $this->SendDebug('Gardena Request Response', $endpoint . ": " . $data, 0);
        return $data;
    }

    /** Get Device Type
     * @param $device
     * @return array
     */
    private function GetDeviceType($device)
    {
        $data = [];
        $model_type = $device['attributes']['modelType']['value'];
        if ($model_type == 'GARDENA smart Irrigation Control') {
            $data = $this->GetIrrigationControlData($device);
        } elseif ($model_type == 'GARDENA smart Sensor') {
            $data = $this->GetSensorInfo($device);
        }
        elseif ($model_type == 'GARDENA smart Water Control') {
            $data = $this->GetWaterControlData($device);
        }
        elseif ($model_type == 'GARDENA smart Mower') {
            $data = $this->GetMoverData($device);
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
        $model_type = $device['attributes']['modelType']['value'];
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

        return ['id' => $id, 'name' => $name, 'serial' => $serial, 'rf_link_state' => $rf_link_state, 'model_type' => $model_type];
    }

    /** Get Irrigation Control Data
     * @param $device
     * @return array
     */
    private function GetIrrigationControlData($device)
    {
        $id = $device['id'];
        $model_type = $device['attributes']['modelType']['value'];
        $name = $device['attributes']['name']['value'];
        $serial = $device['attributes']['serial']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'serial: ' . $serial, 0);
        $rf_link_state = $device['attributes']['rfLinkState']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'RF link state: ' . $rf_link_state, 0);
        return ['id' => $id, 'name' => $name, 'serial' => $serial, 'rf_link_state' => $rf_link_state, 'model_type' => $model_type];
    }

    /** Get Water Control Data
     * @param $device
     * @return array
     */
    private function GetWaterControlData($device)
    {
        $id = $device['id'];
        $model_type = $device['attributes']['modelType']['value'];
        $name = $device['attributes']['name']['value'];
        $serial = $device['attributes']['serial']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'serial: ' . $serial, 0);
        $rf_link_state = $device['attributes']['rfLinkState']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'RF link state: ' . $rf_link_state, 0);
        return ['id' => $id, 'name' => $name, 'serial' => $serial, 'rf_link_state' => $rf_link_state, 'model_type' => $model_type];
    }

    /** Get Mover Data
     * @param $device
     * @return array
     */
    private function GetMoverData($device)
    {
        $id = $device['id'];
        $model_type = $device['attributes']['modelType']['value'];
        $name = $device['attributes']['name']['value'];
        $serial = $device['attributes']['serial']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'serial: ' . $serial, 0);
        $rf_link_state = $device['attributes']['rfLinkState']['value'];
        $this->SendDebug('Gardena Device ' . $name, 'RF link state: ' . $rf_link_state, 0);
        return ['id' => $id, 'name' => $name, 'serial' => $serial, 'rf_link_state' => $rf_link_state, 'model_type' => $model_type];
    }

    /**
     * Liefert alle Geräte.
     *
     * @return array configlist all devices
     */
    private function Get_ListConfiguration()
    {
        $config_list = [];
        $location_id = $this->RequestDataFromParent('location_id');
        if ($location_id != '') {
            $GardenaInstanceIDList = IPS_GetInstanceListByModuleID('{3B073BE1-6556-037C-42FB-6311BC452C68}'); // Gardena Devices
            $snapshot = $this->RequestSnapshotBuffer(); // Get Snapshot
            $this->SendDebug('Gardena Config', $snapshot, 0);
            $payload = json_decode($snapshot, true);
            $counter = count($payload);
            if ($counter > 0) {
                $included = $payload['included'];
                foreach ($included as $device) {
                    $instanceID = 0;
                    $type = $device['type'];
                    if ($type == 'COMMON') {
                        $data = $this->GetDeviceType($device);
                        if(!empty($data))
                        {
                            $id = $data['id'];
                            $name = $data['name'];
                            $serial = $data['serial'];
                            $rf_link_state = $data['rf_link_state'];
                            $model_type = $data['model_type'];
                            foreach ($GardenaInstanceIDList as $GardenaInstanceID) {
                                if (IPS_GetProperty($GardenaInstanceID, 'id') == $id) { // todo  InstanceInterface is not available
                                    $instanceID = $GardenaInstanceID;
                                }
                            }
                            $config_list[] = ["instanceID" => $instanceID,
                                "name" => $name,
                                "serial" => $serial,
                                "rf_link_state" => $rf_link_state,
                                "model_type" => $model_type,
                                "create" => [
                                    [
                                        "moduleID" => "{3B073BE1-6556-037C-42FB-6311BC452C68}",
                                        "configuration" => [
                                            "id" => $id,
                                            "name" => $name,
                                            "serial" => $serial,
                                            "model_type" => $model_type,
                                        ],
                                        "location" => $this->SetLocation()
                                    ]
                                ]
                            ];
                        }
                    }
                }
            }
        }
        return $config_list;
    }

    private function SetLocation()
    {
        $category = $this->ReadPropertyInteger("ImportCategoryID");
        $tree_position[] = IPS_GetName($category);
        $parent = IPS_GetObject($category)['ParentID'];
        $tree_position[] = IPS_GetName($parent);
        do {
            $parent = IPS_GetObject($parent)['ParentID'];
            $tree_position[] = IPS_GetName($parent);
        } while ($parent > 0);
        // delete last key
        end($tree_position);
        $lastkey = key($tree_position);
        unset($tree_position[$lastkey]);
        // reverse array
        $tree_position = array_reverse($tree_position);
        return $tree_position;
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
        $location_id = $this->RequestDataFromParent('location_id');
        if ($location_id == '') {
            $show_config = false;
        } else {
            $show_config = true;
        }
        $visibility_register = false;
        //Check Gardena connection
        $token = $this->GetGardenaToken();
        if ($token == '') {
            $this->SendDebug('Token', $token, 0);
            $visibility_register = true;
        }

        $form = [
            [
                'type'  => 'Image',
                'image' => 'data:image/png;base64, ' . self::PICTURE_LOGO_GARDENA],
            [
                'type' => 'Label',
                'visible' => $visibility_register,
                'caption' => 'Gardena: Please switch to the I/O instance and register with your Gardena account!'
            ],
            [
                'type' => 'Label',
                'caption' => 'category for Gardena devices'
            ],
            [
                'name' => 'ImportCategoryID',
                'type' => 'SelectCategory',
                'caption' => 'category Gardena devices'
            ],
            [
                'name' => 'GardenaConfiguration',
                'type' => 'Configurator',
                'visible' => $show_config,
                'rowCount' => 20,
                'add' => false,
                'delete' => true,
                'sort' => [
                    'column' => 'name',
                    'direction' => 'ascending'
                ],
                'columns' => [
                    [
                        'caption' => 'ID',
                        'name' => 'id',
                        'width' => '200px',
                        'visible' => false
                    ],
                    [
                        'name' => 'name',
                        'caption' => 'name',
                        'width' => 'auto'
                    ],
                    [
                        'name' => 'serial',
                        'caption' => 'serial',
                        'width' => '150px'
                    ],
                    [
                        'name' => 'rf_link_state',
                        'caption' => 'rf link state',
                        'width' => '150px'
                    ],
                    [
                        'name' => 'model_type',
                        'caption' => 'model type',
                        'width' => '300px'
                    ]
                ],
                'values' => $this->Get_ListConfiguration()
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

        $location_id = $this->RequestDataFromParent('location_id');
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
                'caption' => 'Read Gardena configuration:'
            ],
            [
                'type' => 'Button',
                'visible' => $visibility_config,
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