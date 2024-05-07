<?php
namespace App\Services;

use App\Exceptions\ExceptionTypes;
use App\Http\Resources\UserResource;
use App\Mail\Registration;
use App\Mail\Reported;
use App\Models\Admin;
use App\Models\Estate;
use App\Models\User;
use App\Services\ErrorCheckService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

Class UserService{

    function __construct(private ErrorCheckService $errorCheck) {
    }

    public function getUser(){
        $user = User::findByIdWithRoles(Auth::guard('sanctum')->user()->id);
        return new UserResource($user);
    }

    public function getUsers($request){
        $this->errorCheck->checkPaginateRequest($request);
        $users = User::paginate($request->input('limit'), ['*'], 'page', $request->input('page'));
        return [UserResource::collection($users), $users];
    }

    public function patchUser($data){
        $userOrigin = User::findById(Auth::guard('sanctum')->user()->id);
        $userId = $userOrigin->hasRole('Admin') ? $data->id : $userOrigin->id;
        $this->errorCheck->checkIfEmpty($userId, 'id');
        $user = User::findById($userId);

        if (isset($data->email))
        if ($data->email != $user->email && !$user->hasRole('Admin'))
            $this->errorCheck->checkIfAlreadyExisting(new User, $data->email,'email');

        $patchData = (object) Arr::only((array) $data, User::$patchable);
        
        foreach ($patchData as $key => $value)
            $user->$key = $value;

        if (isset($data->password))
            $user->password = bcrypt($data->password);

        $user->save();
    }

    public function delete($id){
        $user = User::findById($id);
        $user->delete();
    }

    public function create($data){
        $this->errorCheck->checkIfAlreadyExisting(new User, $data->email,'email');

        $user = new User((array)$data);
        $user->password = bcrypt($data->password);

        $user->watched_estates = $user->reported_estates = [];
       // Mail::to($user->email)->send(new Registration($user));
        $user->assignRole('User');
        $user->save();
        return $user->createToken("accessToken")->plainTextToken;
    }

    public function login($data){
        $this->errorCheck->checkIfEmpty($data->email, 'email');
        $this->errorCheck->checkIfExisting(new User, $data->email, 'email');
        $user = User::findByEmail($data->email);
        $this->errorCheck->checkIfMashMatching($data->password, $user->password, 'heslo');
        return $user->createToken("accessToken")->plainTextToken;
    }

    public function addToFavorites($data){
        $user = User::findById(Auth::guard('sanctum')->user()->id);
        $temp = $user->watched_estates;
        
        if (!in_array($data->uuid, $user->watched_estates)){
            $temp[] = $data->uuid;
            $user->watched_estates = $temp;
            $user->save();
            return true;
        }
       
        $temp = array_diff($user->watched_estates, [$data->uuid]);
        $user->watched_estates =  $temp;
        $user->save();
        return false;
    }

    public function reportEstate($data){
        $estate = Estate::findByUuid($data->uuid);
        $estate->reported += 1;
        $estate->save();
       // Mail::to(Admin::getAdmin()->email)->send(new Reported($user, $data));
    }
}
?>