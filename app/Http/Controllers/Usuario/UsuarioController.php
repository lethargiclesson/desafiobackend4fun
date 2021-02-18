<?php

namespace App\Http\Controllers\Usuario;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UsuarioController extends Controller
{
    public function registro(Request $request)
    {
        $valitador = Validator::make($request->all(), [
            'nome' => ['required',  'min:5'],
            'password' => ['required', 'min:8'],
            'email' => ['required', 'email', 'unique:users,email'],
            'cpf' => ['required', 'min:11', 'max:11', 'unique:users,cpf'],
            'tipo' => ['required', Rule::in(['usuario', 'lojista'])],
            'cnpj' => ['required_unless:tipo,usuario', 'min:14', 'max:14']
        ]);

        if ($valitador->fails()) {
            return response()->json(['error' => $valitador->errors()], 400);
        }

        try {
            $usuario = new User();
            $usuario->nome      = $request->nome;
            $usuario->password  = Hash::make($request->password);
            $usuario->email     = $request->email;
            $usuario->cpf       = $request->cpf;
            $usuario->tipo      = $request->tipo;
            $usuario->cnpj      = $request->cnpj;

            $usuario->save();

            $usuario->balance()->create();

            return response()->json(['usuario' => 'Cadastro realizado com sucesso!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
