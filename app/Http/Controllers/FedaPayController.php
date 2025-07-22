<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Bill;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class FedaPayController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $amount = $request->input('amount');
        $customer_email = $request->input('email');
        $customer_name = $request->input('name');

        $client = new Client();
        $apiKey = env('FEDAPAY_API_KEY');
        $env = env('FEDAPAY_ENV', 'sandbox');
        $baseUrl = $env === 'live' ? 'https://api.fedapay.com/v1' : 'https://sandbox-api.fedapay.com/v1';

        try {
            $response = $client->post("$baseUrl/transactions", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'description' => 'Paiement consultation',
                    'amount' => $amount,
                    'currency' => ['iso' => 'XOF'],
                    'customer' => [
                        'firstname' => $customer_name,
                        'email' => $customer_email,
                    ],
                    'callback_url' => route('fedapay.callback'),
                    'return_url' => route('fedapay.return'),
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $redirectUrl = $data['transaction']['url'] ?? null;

            if ($redirectUrl) {
                return redirect($redirectUrl);
            } else {
                return back()->with('error', "Impossible d'obtenir l'URL de paiement FedaPay.");
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur FedaPay : ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        // Récupérer les données envoyées par FedaPay
        $payload = $request->all();
        // Exemple : $payload['transaction']['status'], $payload['transaction']['amount'], etc.
        $status = $payload['transaction']['status'] ?? null;
        $amount = $payload['transaction']['amount'] ?? null;
        $billId = $payload['transaction']['custom_data']['bill_id'] ?? null;
        $method = 'fedapay';
        $paymentDate = now();

        if ($status === 'approved' && $billId) {
            $bill = Bill::find($billId);
            if ($bill) {
                $bill->is_paid = true;
                $bill->save();
                // Enregistrer le paiement
                Payment::create([
                    'bill_id' => $bill->id,
                    'amount' => $amount,
                    'method' => $method,
                    'payment_date' => $paymentDate,
                ]);
                // Envoi du reçu PDF par email
                $user = $bill->patient->user ?? null;
                if ($user && $user->email) {
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('bills.receipt_pdf', compact('bill'));
                    Mail::send([], [], function ($message) use ($user, $pdf, $bill) {
                        $message->to($user->email)
                            ->subject('Votre reçu de paiement MediConnectHub')
                            ->setBody('Bonjour,\n\nVeuillez trouver en pièce jointe votre reçu de paiement.\n\nMerci pour votre confiance.\nMediConnectHub', 'text/plain')
                            ->attachData($pdf->output(), 'recu-facture-'.$bill->id.'.pdf');
                    });
                }
                return response()->json(['status' => 'success', 'message' => 'Paiement validé, facture mise à jour et reçu envoyé.']);
            }
        }
        return response()->json(['status' => 'error', 'message' => 'Paiement non validé ou facture introuvable.']);
    }

    public function return(Request $request)
    {
        $user = auth()->user();
        $bill = null;
        if ($user && $user->patient) {
            $bill = \App\Models\Bill::where('patient_id', $user->patient->id)
                ->where('is_paid', true)
                ->latest('updated_at')
                ->first();
        }
        return view('fedapay.return', compact('bill'));
    }

    public function downloadReceipt($billId)
    {
        $bill = \App\Models\Bill::findOrFail($billId);
        $pdf = Pdf::loadView('bills.receipt_pdf', compact('bill'));
        return $pdf->download('recu-facture-'.$bill->id.'.pdf');
    }
}
