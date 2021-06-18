@extends('layouts.app')

@php
    $userInfo = $user->userInfo
@endphp

@section('content')
<div class="container profile-page">
    <form class="form-floating">
        <div class="group">
            <label for="last_name">Фамилия</label>
            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Фамилия" value="{{ $userInfo->last_name }}" disabled>

            <label for="first_name">Имя</label>
            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Имя" value="{{ $userInfo->first_name }}" disabled>

            <label for="middle_name">Отчество</label>
            <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="Отчество" value="{{ $userInfo->middle_name }}" disabled>

            <label for="birthdate">Дата рождения</label>
            <input type="date" class="form-control" id="birthdate" name="birthdate" placeholder="Дата рождения" value="{{ $userInfo->birthdate }}" disabled>
        </div>
        <div class="group">
            <label for="role">Роль</label>
            <input type="text" class="form-control" id="role" placeholder="Роль" value="{{ $user->role->ru_name }}" disabled>

            <label for="militaryObject">Воинская часть</label>
            <input type="text" class="form-control" id="militaryObject" placeholder="Воинская часть" value="{{ $user->object->name }}" disabled>
      
            <label for="position">Должность</label>
            <input type="text" class="form-control" id="position" name="position" placeholder="Должность" value="{{ $userInfo->position }}" disabled>

            <label for="military_rank">Звание</label>
            <input type="text" class="form-control" id="military_rank" name="military_rank" placeholder="Звание" value="{{ $userInfo->military_rank }}" disabled>
        </div>
    </form>
</div>
@endsection
