<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use SkipsFailures;

    public function model(array $row)
    {
        // Validasi dan upsert logic
        // Jika username atau email sudah ada, maka di-skip karena kita pakai WithValidation.
        // Jika lolos validasi, maka insert.

        return new User([
            'name'     => $row['nama'],
            'username' => $row['username'],
            'email'    => $row['email'] ?? null,
            'password' => Hash::make($row['password']),
            'kelas_id' => $row['id_kelas'],
            'role'     => 'siswa',
        ]);
    }

    public function rules(): array
    {
        return [
            'username' => 'required|unique:users,username',
            'email'    => 'nullable|email|unique:users,email',
            'nama'     => 'required',
            'password' => 'required',
            'id_kelas' => 'required|exists:kelas,id',
        ];
    }
}
