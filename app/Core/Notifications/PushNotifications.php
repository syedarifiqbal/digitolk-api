<?php


namespace App\Core\Notifications;


use Illuminate\Support\Collection;

class PushNotifications
{
    protected $url;
    protected $deviceIds;
    protected $headers;
    protected $result;
    protected $title;
    protected $description;
    protected $isFail;
    protected $isSuccess;
    protected $payload;
    protected $notificationType;

    function __construct()
    {
        $this->url = 'https://fcm.googleapis.com/fcm/send';
        $this->deviceIds = [];
        $this->headers = ['Authorization: key=AAAAopitWIA:APA91bFyVKjTZ464PKWKCHB0AIW7PS1uiiW77pUWqgiipeBUNY79VI2J3rhzNoIxXOMR6PhcxB10Gcqqi9x1nRTbTVoH6-M1shFkoW4_epmQsWAb25DMKwLlLSqHzwWighNb1OP5BP63', 'Content-Type: application/json'];
    }

    function addDevice($id, $device_type)
    {
        $this->deviceIds[$id] = $device_type;
        return $this;
    }

    function addDevices($ids)
    {
        foreach ($ids as $id => $device_type)
            $this->deviceIds[$id] = $device_type;
        return $this;
    }

    function send($title, $description, $type = "", array $payload = [])
    {
        $this->payload = $payload;

        $this->title = $title;
        $this->description = $description;
        $this->notificationType = $type;

        $AndroidDevices = collect($this->deviceIds)->filter(function ($device) {
            return strtoupper($device) === 'ANDROID';
        });

        $IOSDevices = collect($this->deviceIds)->filter(function ($device) {
            return strtoupper($device) === 'IOS';
        });

        $this->sendAndroidNotification($AndroidDevices);

        $this->sendIOSNotification($IOSDevices);
    }

    private function preparePayload(Collection $devicesIds, $device_type)
    {
        $fields = array('registration_ids' => $devicesIds->keys());

        /*if(strtoupper($device_type) === 'ANDROID') {

            $fields['data'] = array_merge([
                'title' => $this->title,
                'body'  => $this->description,
                'type' => $this->notificationType,
            ], $this->payload);
            $fields['priority'] = 'high';
        }

        if(strtoupper($device_type) === 'IOS')
        {*/

        $fields['data'] = array_merge([
            'title' => $this->title,
            'body'  => $this->description,
            'type' => $this->notificationType,
        ], $this->payload);
        $fields['priority'] = 'high';

        $fields['notification'] = array_merge([
            'title' => $this->title,
            'body' => $this->description,
        ], $this->payload);
        /*}*/

        return json_encode($fields);
    }

    private function sendAndroidNotification(Collection $AndroidDevices)
    {
        if ($AndroidDevices->isNotEmpty()) {
            $payload = $this->preparePayload($AndroidDevices, "ANDROID");
            $this->push($payload);
        }
    }

    private function sendIOSNotification($IOSDevices)
    {
        $payload = $this->preparePayload($IOSDevices, "IOS");
        $this->push($payload);
    }

    public function push($data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $result = json_decode(curl_exec($ch));


        if ($result) {
            $this->result = $result;
            $this->isSuccess = !!$result->success;
            $this->isFail = !!$result->failure;

            if (array_key_exists('error', $result->results))
                $this->error = $result->results['errors'];
        }
    }

    public function getResults()
    {
        return $this->result;
    }

    public function __toString()
    {
        return json_encode($this->result);
    }
}
