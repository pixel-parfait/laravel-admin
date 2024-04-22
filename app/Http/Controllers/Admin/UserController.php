<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * @var array
     */
    private $formOptions = [];

    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');

        $this->formOptions = [
            'roles' => config('settings.roles'),
        ];
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request): Response
    {
        $usersQuery = User::where('locked', false)
            ->orderBy('created_at', 'desc');

        if ($request->search) {
            $usersQuery = User::where('name', 'like', "%{$request->search}%");
        }

        $users = $usersQuery
            ->paginate(25)
            ->onEachSide(2)
            ->withQueryString()
            ->through(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames()->transform(fn ($role) => config("settings.roles.{$role}"))->implode(', '),
                'last_login_at' => $user->last_login_at,
            ]);

        return Inertia::render('Users/Index', [
            'filters' => $request->all('search'),
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        return Inertia::render('Users/Create', [
            'options' => $this->formOptions,
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $this->getData($request);

        $user = User::create([
            ...$data,
            'email_verified_at' => Carbon::now(),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', "L'utilisateur a été créé.");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): Response
    {
        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->getRoleNames()?->first(),
                'password' => null,
            ],
            'options' => $this->formOptions,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $this->getData($request);

        $user->update($data);

        $user->assignRole($request->role);

        return redirect()->back()->with('success', "L'utilisateur a été mis à jour.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        // Check if the user is not trying to delete theirself
        if ($user->id === $request->user()->id) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "L'utilisateur a été supprimé.");
    }

    /**
     * Retrieve the formatted form data from request.
     */
    private function getData(mixed $request): array
    {
        $data = [
            'email' => $request->email,
            'name' => $request->name,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        return $data;
    }
}
