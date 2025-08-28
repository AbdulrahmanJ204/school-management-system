<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyStudentAttendence extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
// school day id
// query params : grade , section
// return
// { data :
// { students [
//      {id  ,name , status}
// ]
//

// to update send student id , school day id , status

// {
//    school day id
//  [{
//  student id
//  status
//}]
//}
