<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

use Livewire\Component;

class Edit extends Component
{
    public User $user;
    public string $name;
    public string $username;
    public string $status;
    public string $password = '';
    public string $password_confirmation = '';
    public string $asda = '';
    public $roles;
    public $selected_roles;


    protected $rules = [
        'name' => 'required|string',
        'username' => 'required|string',
        'selected_roles' => 'required|array',
    ];

    public function mount(User $user)
    {
        $this->roles = Role::all();
        $this->name = $user->name;
        $this->username = $user->username;
        $this->selected_roles = $user->getRoleNames();
        $this->status = $user->status ? "true" : "false";
    }

    public function render()
    {
        return view('livewire.user.edit');
    }

    public function save()
    {
        $this->validate();

        $this->user->name = $this->name;
        $this->user->username = $this->username;
        $this->user->status = $this->status == "true" ? true : false;

        if ($this->password || $this->password_confirmation) {

            $input = [
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ];

            $validator = Validator::make($input, [
                'password' => ['required', Password::defaults()],
                'password_confirmation' => 'required|same:password'
            ]);

            $validator->safe()->only(['password_confirmation', 'password']);
            $this->user->password = $this->password;
        }

        $this->user->save();
        $this->user->syncRoles($this->selected_roles);


        return redirect()->route('users.index');
    }


    public function getSelected($status)
    {
        return  $status ? 'selected' : '';
    }
}
