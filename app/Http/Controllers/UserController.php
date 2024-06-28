<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    protected $pageOptions = [5, 10, 25, 50, 100];

    public function index()
    {
        $limit = request('limit', 10);
        $page = request('page', 1);
        $builder = QueryBuilder::for(User::class)
            ->defaultSort('-id')
            ->allowedSorts(['id', 'name', 'email'])
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
        $users = $builder
            ->clone()
            ->paginate(perPage: $limit, page: $page)
            ->through(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->format('d/m/Y H:i').'h',
            ])
            ->withQueryString();

        return Inertia::render('Dashboard', [
            'users' => $users,
            'pageOptions' => $this->pageOptions,
            'limit' => $users->perPage(),
            'allIds' => inertia()->lazy(fn () => $builder->pluck('id')),
            'columns' => [
                ['key' => 'id', 'label' => 'ID', 'visible' => true],
                ['key' => 'name', 'label' => 'Name', 'visible' => true],
                ['key' => 'email', 'label' => 'Email', 'visible' => true],
                ['key' => 'created_at', 'label' => 'Created At', 'visible' => true],
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
