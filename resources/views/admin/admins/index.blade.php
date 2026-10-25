@extends('layouts.admin')

@section('content')
    <div class="container-xxl py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Admin Management</h3>

            <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                Add Administrator
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($admins as $admin)
                            <tr>
                                <td>{{ $admin->name }}</td>
                                <td>{{ $admin->email }}</td>

                                <td>
                                    @foreach ($admin->roles as $role)
                                        <span class="badge bg-secondary">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </td>

                                <td>
                                    @if ($admin->id !== auth()->id() && !$admin->can('manage admins') && !$admin->can('manage store'))
                                        <a href="{{ route('admin.admins.edit', $admin) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            Edit
                                        </a>
                                    @else
                                        <span class="text-muted">Protected</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    No administrators found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $admins->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
