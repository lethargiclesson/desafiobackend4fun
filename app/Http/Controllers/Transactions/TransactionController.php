<?php

namespace App\Http\Controllers\Transactions;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function transaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payer' => ['required', Rule::exists('users', 'id')->where(function ($query) {
                return $query->where('tipo', 'usuario');
            })],
            'payee' => ['required', Rule::exists('users', 'id')->where(function ($query) {
                return $query->where('tipo', 'lojista');
            })],
            'value' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        try {
            $user = User::where('id', $request->payer)->where('tipo', 'usuario')->first();
            $seller = User::where('id', $request->payee)->where('tipo', 'lojista')->first();

            if (!$user) throw new \Exception('Comprador não encontrado', 404);
            if (!$seller) throw new \Exception('Lojista não encontrado', 404);
            if ($user->balance()->first()->balance < $request->value) throw new \Exception('Creditos insuficientes.', 406);

            $transacao = new Transaction();
            $transacao->payer_id = $request->payer;
            $transacao->payee_id = $request->payee;
            $transacao->amount   = $request->value;

            $oldBalance = $user->balance()->first()->balance;
            $newBalance = $oldBalance - $request->value;

            $user->balance()->update(['balance' => $newBalance]);
            $transacao->save();


            $validate = json_decode($this->validateTransaction())->message;

            if ($validate !== 'Autorizado') {
                $user->balance()->update(['balance' => $oldBalance]);
                $transacao->status = 'cancelado';
                $transacao->save();
                return response()->json(['error' => 'Transação não aprovada!'], 406);
            }

            $transacao->status = 'aprovado';
            $transacao->save();

            $sellerNewBalance = $seller->balance()->first()->balance + $request->value;
            $seller->balance()->update(['balance' => $sellerNewBalance]);

            return response()->json(['success' => 'Transação realizada com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    private function validateTransaction()
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}
