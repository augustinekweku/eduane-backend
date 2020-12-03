<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Role;
use App\Models\User;
use App\Models\Mealplan;
use Illuminate\Http\Request;
use App\Models\Mealplanduration;
use App\Models\Mealplanpackages;
use App\Models\sharedmealpackages;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(Request $request) {
        // first check if u are logged in as admin user
        if (!Auth::check() && $request->path() != 'login') {
            return redirect('/login');
        }
        if (!Auth::check() && $request->path() == 'login') {
            return view('welcome');
        }
        //you are already logged in so check if you are an admin user
        $user = Auth::user();
        // return $request->path();
        // return $user->role_id;
        if ($request->path() == 'login') {
            return redirect('/');
        }

        return $this->checkForPermission($user, $request);
    }

    public function checkForPermission($user, $request) {
        $permission = json_decode($user->role->permission);
        $hasPermission = false;
        if(!$permission) return view('welcome'); 
        foreach ($permission as $p) {
            if ($p->name==$request->path()) {
                if ($p->read) {
                    $hasPermission = true;
                }
            }
        } 
        if($hasPermission) return view('welcome');
        return view('notfound'); 
    }
    public function createRole(Request $request) {
        //validate request
        $this->validate($request, [
            'roleName' => 'required',
        ]);
        //return $request->roleName;
        return Role::create([
            'roleName' => $request->roleName
        ]);
    }

    public function getRoles() {
        return Role::orderBy('id', 'desc')->get();
    }

    public function editRole(Request $request) {
        //validate request
        $this->validate($request, [
            'roleName' => 'required'
        ]);
        return Role::where('id', $request->id)->update([
            'roleName' => $request->roleName
        ]);
}

    public function deleteRole(Request $request) {
        return Role::where('id', $request->id)->delete();
    }

    public function getUsers() {
        return User::orderBy('id', 'desc')->get();
        }

    public function createUser(Request $request) {
                //validate request
            $this->validate($request, [
                'firstName' => 'required',
                'lastName' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'role_id' => 'required',
            ]);
            $password = bcrypt($request->password);
            $user = User::create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'password' => $password,
            'role_id' => $request->role_id,
        ]);
            return $user;
        }
        public function editUser(Request $request) {
            //validate request
            $this->validate($request, [
                'firstName' => 'required',
                'lastName' => 'required',
                'email' => "bail|required|email|unique:users,email,$request->id",
                'password' => 'nullable|min:8',
                'role_id' => 'required',
            ]);

            $data = [
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'email' => $request->email,
                'role_id' => $request->role_id,
            ];
            if ($request->password) {
                $password = bcrypt($request->password);
                $data['password'] = $password; 
            }
            
            $user = User::where('id',$request->id)->update($data);
            return $user;
        
    }
    public function deleteUser(Request $request){
        return User::where('id', $request->id)->delete();

    }

    public function adminLogin(Request $request) {
        //validate request
        $this->validate($request, [
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:6',
        ]);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, ])) {
            $user = Auth::user();
            //return $user;
            if ($user->role->isAdmin == 0) {
                Auth::logout(); 
                return response()->json([
                    'msg' => 'Incorrect Login details: Not Admin'
                ], 401);
            }
            
            return response()->json([
                'msg' => 'You are logged in',
                'user' => $user
            ]);
        }else {
            return response()->json([
                'msg' => 'Incorrect Login details '
            ], 401);    }
    }


    public function logout() {
        Auth::logout();
        return redirect('/login');
    }

    public function assignRole(Request $request) {
        //validate request
        $this->validate($request, [
            'permission' => 'required',
            'id' => 'required'
        ]);
        return Role::where('id', $request->id)->update([
            'permission' => $request->permission
        ]);    
}

public function upload(Request $request) {
    $this->validate($request, [
        'file' => 'required|mimes:jpeg,jpg,png',
    ]);
    $picName = time(). '.' . $request->file->extension();
    $request->file->move(public_path('uploads'), $picName);
    return $picName;
}

public function deleteImage(Request $request) {
    $fileName = $request->imageName;
    $this->deleteFileFromServer($fileName, false);
    return 'done';
}
public function deleteFileFromServer($fileName, $hasFullPath = false) {
    if (!$hasFullPath) {
        $filePath = public_path().$fileName;
    }
    if (file_exists($filePath)) {
        @unlink($filePath);
    }
    return;
}
public function createPlan(Request $request) {
    $user = Auth::user();
    //validate request
    $this->validate($request, [
        'title' => 'required',
        'description' => 'required',
        'carbs' => 'required',
        'protein' => 'required',
        'calories' => 'required',
        'fat' => 'required',
        'featuredImage' => 'required',

    ]);
    return Mealplan::create([
        'title' => $request->title,
        'description' => $request->description,
        'carbs' => $request->carbs,
        'protein' => $request->protein,
        'fat' => $request->fat,
        'calories' => $request->calories,
        'featuredImage' => $request->featuredImage,
        'user_id' => $user->id
    ]);
}

public function getPlans() {
    return Mealplan::orderBy('id', 'desc')->get();
}
public function editPlan(Request $request) {
    //validate request
            $this->validate($request, [
                'title' => 'required',
                'description' => 'required',
                'carbs' => 'required',
                'protein' => 'required',
                'calories' => 'required',
                'fat' => 'required',
                'featuredImage' => 'required'
            ]);
            $data = [
                'title' => $request->title,
                'description' => $request->description,
                'carbs' => $request->carbs,
                'protein' => $request->protein,
                'calories' => $request->calories,
                'fat' => $request->fat,
                'featuredImage' => $request->featuredImage,
                'user_id' => $request->user_id,
            ];            
            $plan = Mealplan::where('id',$request->id)->update($data);
            return $plan;
}

public function deletePlan(Request $request) {
    return Mealplan::where('id', $request->id)->delete();
}

public function addDuration(Request $request) {
    $user = Auth::user();
    //validate request
    $this->validate($request, [
        'title' => 'required',
        'description' => 'required'
    ]);
    return Mealplanduration::create([
        'title' => $request->title,
        'description' => $request->description,
        'user_id' => $user->id
    ]);
}

public function getDurations() {
    return Mealplanduration::orderBy('id', 'desc')->get();
}


public function editDuration(Request $request) {
    //validate request
    $this->validate($request, [
        'title' => 'required',
        'description' => 'required',
        
    ]);
    return Mealplanduration::where('id', $request->id)->update([
        'title' => $request->title,
        'description' => $request->description,
    ]);
}

public function deleteDuration(Request $request) {
    return Mealplanduration::where('id', $request->id)->delete();
}

public function createPackage(Request $request) {
    //validate request
    $this->validate($request, [
        'title' => 'required',
        'recipe' => 'required',
        'price' => 'required',
        'featuredImage' => 'required',
        'mealplans_id' => 'required',
        'mealplanduration_id' => 'required',
    ]);
$mealplans = $request->mealplans_id;
DB::beginTransaction();
$user = Auth::user();

try {
    foreach($mealplans as $m){
    $package = ([
        'title' => $request->title,
        'recipe' => $request->recipe,
        'price' => $request->price,
        'featuredImage' => $request->featuredImage,
        'user_id' => $user->id ,
        'mealplan_id' => $m,
        'mealplanduration_id' => $request->mealplanduration_id,
    ]);
    
    Mealplanpackages::insert($package);
    DB::commit();

    
                }
    return 'done';
} catch  (Exception $e) {
    DB::rollback();
    return $e;
}

}



}