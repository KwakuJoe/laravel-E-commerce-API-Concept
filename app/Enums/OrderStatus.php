<?php

namespace App\Enums;



enum OrderStatus: string
{
   case PROCESSING = "Processing";
   case SHIPPED = "Shipped";
   case DELIVERED = "Delivered";
   case CANCELLED = "Cancelled";
}

