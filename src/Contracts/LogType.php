<?php

namespace YektaSmart\IotServer\Contracts;

enum LogType: string
{
    case ProductChanged = 'yekta-smart.iot.device.product-changed';
    case FirmwareChanged = 'yekta-smart.iot.device.firmware-changed';
    case HardwareChanged = 'yekta-smart.iot.device.hardware-changed';
}
