<?php
declare(strict_types=1);

class GardenaConfigurator extends IPSModule
{
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
                                if (IPS_GetProperty($GardenaInstanceID, 'id') == $id) {
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