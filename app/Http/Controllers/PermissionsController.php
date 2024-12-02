<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use App\Models\Permissions;
use App\Http\Requests\RoleRequest;
use App\Traits\HasCrud;
use Inertia\Inertia;

class PermissionsController extends Controller
{
    use HasCrud;

    public function __construct()
    {
        $this->model=Permissions::class;
        $this->view='Permissions/Index';
        $this->data=['permissions'=>$this->model::all()];

    }

    // This will use the UserRequest for validation
    public function update(PermissionRequest $request, $id)
    {
       $data=$request->validated();
       $data['updated_at']=now();
        $this->model::find($id)->update($data);
        $data=$this->model::all();
        return response()->json(['check'=>true,'data'=>$data]);
    }
}
