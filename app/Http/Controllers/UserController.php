<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    protected array $pageOptions = [5, 10, 25, 50, 100];

    public function index()
    {
        $limit = request('limit', 10);
        $page = request('page', 1);

        $builder = QueryBuilder::for(User::class)
            ->defaultSort('-created_at')
            ->allowedSorts(['id', 'name', 'email', 'created_at'])
            ->allowedFilters([
                'name',
                'email',
                AllowedFilter::callback('search', function (Builder $query, $value) {
                    $query->where(function (Builder $query) use ($value) {
                        $query->where('name', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%");
                    });
                }),
            ]);

        /** @var Collection $paginatedUsers */
        $paginatedUsers = $builder
            ->clone()
            ->paginate(perPage: $limit, page: $page);

        $users = $paginatedUsers
            ->through(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->format('d F Y'),
            ])
            ->withQueryString();

        return Inertia::render('User/Index', [
            'users' => $users,
            'pageOptions' => $this->pageOptions,
            'limit' => $users->perPage(),
            'allIds' => inertia()->lazy(fn () => $builder->pluck('id')),
            'columns' => [
                ['key' => 'id', 'label' => 'ID', 'visible' => true, 'sortable' => true],
                ['key' => 'name', 'label' => 'Name', 'visible' => true, 'sortable' => true],
                ['key' => 'email', 'label' => 'Email', 'visible' => true, 'sortable' => true],
                ['key' => 'created_at', 'label' => 'Created At', 'visible' => true, 'sortable' => true],
            ],
            'filters' => ['name', 'email', 'search'],
            'defaultSort' => '-created_at',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('User/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|max:255|confirmed',
        ]);
        $user = User::query()->create($data);

        session()->flash('message', [
            'message' => "Buat user {$user->id} berhasil.",
            'type' => 'success',
        ]);

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return Inertia::render('User/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->format('d/m/Y H:i'),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return Inertia::render('User/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->format('d/m/Y H:i'),
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,id,'.$user->id,
            'password' => 'nullable|string|min:8|max:255|confirmed',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }
        $user->fill($data)->save();

        session()->flash('message', [
            'message' => 'Update user berhasil.',
            'type' => 'success',
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        session()->flash('message', [
            'message' => "User id {$user->id} berhasil dihapus.",
            'type' => 'success',
        ]);

        return redirect()->back();
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|exists:users,id',
        ]);
        User::query()->whereIn('id', $request->ids)->delete();

        $idsString = collect($request->ids)->implode(',');
        session()->flash('message', [
            'message' => "User id {$idsString} berhasil dihapus.",
            'type' => 'success',
        ]);

        return redirect()->back();
    }
}
