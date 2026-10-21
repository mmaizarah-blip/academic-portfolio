package com.example.smartrent;

import android.content.Intent;
import android.graphics.Color;
import android.graphics.Typeface;
import android.net.Uri;
import android.os.Bundle;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.google.firebase.firestore.FirebaseFirestore;

public class ViewPaymentsActivity extends AppCompatActivity {

    LinearLayout paymentContainer;
    TextView tvPaymentSummary;
    FirebaseFirestore db;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_view_payments);

        paymentContainer = findViewById(R.id.paymentContainer);
        tvPaymentSummary = findViewById(R.id.tvPaymentSummary);
        db = FirebaseFirestore.getInstance();

        loadPayments();
    }

    private void loadPayments() {
        db.collection("properties")
                .get()
                .addOnSuccessListener(queryDocumentSnapshots -> {

                    int pendingPayments = 0;
                    int paidPayments = 0;
                    int totalRevenue = 0;

                    if (queryDocumentSnapshots.isEmpty()) {
                        tvPaymentSummary.setText(
                                "💰 Total Revenue: RM 0\n" +
                                        "💳 Pending Payments: 0\n" +
                                        "✅ Paid Payments: 0"
                        );

                        TextView emptyText = new TextView(this);
                        emptyText.setText("No payment records found.");
                        emptyText.setTextSize(18);
                        emptyText.setTextColor(Color.parseColor("#777777"));
                        paymentContainer.addView(emptyText);
                        return;
                    }

                    for (int i = 0; i < queryDocumentSnapshots.size(); i++) {

                        var document = queryDocumentSnapshots.getDocuments().get(i);

                        String paymentStatus = document.getString("paymentStatus");

                        if (paymentStatus == null) {
                            paymentStatus = "Pending";
                        }

                        Object amountObj = document.get("paymentAmount");
                        int amount = 0;

                        if (amountObj instanceof Number) {
                            amount = ((Number) amountObj).intValue();
                        }

                        if (paymentStatus.equalsIgnoreCase("Paid")) {
                            paidPayments++;
                            totalRevenue += amount;
                        } else {
                            pendingPayments++;
                        }
                    }

                    tvPaymentSummary.setText(
                            "💰 Total Revenue: RM " + totalRevenue +
                                    "\n💳 Pending Payments: " + pendingPayments +
                                    "\n✅ Paid Payments: " + paidPayments
                    );

                    for (int i = 0; i < queryDocumentSnapshots.size(); i++) {

                        var document = queryDocumentSnapshots.getDocuments().get(i);

                        String title = document.getString("title");
                        String paymentStatus = document.getString("paymentStatus");
                        String billCode = document.getString("toyyibpayBillCode");

                        if (title == null) title = "Untitled Property";
                        if (paymentStatus == null) paymentStatus = "Pending";

                        Object amountObj = document.get("paymentAmount");
                        int amount = 0;

                        if (amountObj instanceof Number) {
                            amount = ((Number) amountObj).intValue();
                        }

                        LinearLayout card = new LinearLayout(this);
                        card.setOrientation(LinearLayout.VERTICAL);
                        card.setPadding(32, 28, 32, 28);
                        card.setBackgroundColor(Color.WHITE);

                        LinearLayout.LayoutParams params =
                                new LinearLayout.LayoutParams(
                                        ViewGroup.LayoutParams.MATCH_PARENT,
                                        ViewGroup.LayoutParams.WRAP_CONTENT
                                );

                        params.setMargins(0, 0, 0, 20);
                        card.setLayoutParams(params);
                        card.setElevation(8f);

                        TextView titleText = new TextView(this);
                        titleText.setText("🏠 " + title);
                        titleText.setTextSize(20);
                        titleText.setTypeface(null, Typeface.BOLD);
                        titleText.setTextColor(Color.parseColor("#0F5F3A"));

                        TextView statusText = new TextView(this);
                        statusText.setText("Payment Status: " + paymentStatus);
                        statusText.setTextSize(15);
                        statusText.setTypeface(null, Typeface.BOLD);
                        statusText.setPadding(0, 10, 0, 0);

                        if (paymentStatus.equalsIgnoreCase("Paid")) {
                            statusText.setTextColor(Color.parseColor("#1E7A5A"));
                        } else {
                            statusText.setTextColor(Color.parseColor("#F9A825"));
                        }

                        TextView detailText = new TextView(this);
                        detailText.setText(
                                "Amount: RM " + amount +
                                        "\nMethod: ToyyibPay" +
                                        "\nBill Code: " + (billCode == null ? "Not generated" : billCode)
                        );
                        detailText.setTextSize(15);
                        detailText.setTextColor(Color.parseColor("#333333"));
                        detailText.setPadding(0, 10, 0, 12);

                        Button btnOpenBill = new Button(this);
                        btnOpenBill.setText("Open Payment Page");
                        btnOpenBill.setTextColor(Color.WHITE);
                        btnOpenBill.setBackgroundColor(Color.parseColor("#1E7A5A"));

                        if (billCode != null && !billCode.isEmpty()) {
                            btnOpenBill.setOnClickListener(v -> {
                                String paymentUrl = "https://toyyibpay.com/" + billCode;
                                Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(paymentUrl));
                                startActivity(intent);
                            });
                        } else {
                            btnOpenBill.setEnabled(false);
                            btnOpenBill.setText("Bill Not Generated");
                        }

                        Button btnPaid = new Button(this);
                        btnPaid.setText("Mark as Paid");
                        btnPaid.setTextColor(Color.parseColor("#0F5F3A"));
                        btnPaid.setBackgroundColor(Color.parseColor("#D9EDE4"));

                        btnPaid.setOnClickListener(v -> {
                            document.getReference()
                                    .update("paymentStatus", "Paid")
                                    .addOnSuccessListener(aVoid -> {
                                        Toast.makeText(this, "Payment marked as Paid", Toast.LENGTH_SHORT).show();
                                        recreate();
                                    });
                        });

                        card.addView(titleText);
                        card.addView(statusText);
                        card.addView(detailText);
                        card.addView(btnOpenBill);

                        if (!paymentStatus.equalsIgnoreCase("Paid")) {
                            card.addView(btnPaid);
                        }

                        paymentContainer.addView(card);
                    }

                })
                .addOnFailureListener(e ->
                        Toast.makeText(this, "Failed: " + e.getMessage(), Toast.LENGTH_LONG).show());
    }
}