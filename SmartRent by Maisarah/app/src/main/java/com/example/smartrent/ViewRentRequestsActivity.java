package com.example.smartrent;

import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.google.firebase.firestore.FirebaseFirestore;

public class ViewRentRequestsActivity extends AppCompatActivity {

    LinearLayout requestContainer;
    FirebaseFirestore db;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_view_rent_requests);

        requestContainer = findViewById(R.id.requestContainer);
        db = FirebaseFirestore.getInstance();

        loadRequests();
    }

    private void loadRequests() {
        db.collection("rent_requests")
                .get()
                .addOnSuccessListener(queryDocumentSnapshots -> {

                    requestContainer.removeAllViews();

                    if (queryDocumentSnapshots.isEmpty()) {
                        TextView emptyText = new TextView(this);
                        emptyText.setText("No rental requests yet.");
                        emptyText.setTextSize(18);
                        emptyText.setTextColor(Color.parseColor("#777777"));
                        requestContainer.addView(emptyText);
                        return;
                    }

                    queryDocumentSnapshots.forEach(document -> {

                        String propertyTitle = document.getString("propertyTitle");
                        String name = document.getString("name");
                        String phone = document.getString("phone");
                        String moveInDate = document.getString("moveInDate");
                        String message = document.getString("message");
                        String status = document.getString("status");

                        if (propertyTitle == null) propertyTitle = "Unknown Property";
                        if (name == null) name = "No name";
                        if (phone == null) phone = "No phone";
                        if (moveInDate == null) moveInDate = "No move-in date";
                        if (message == null || message.isEmpty()) message = "No message";
                        if (status == null) status = "Pending";

                        LinearLayout card = new LinearLayout(this);
                        card.setOrientation(LinearLayout.VERTICAL);
                        card.setPadding(32, 28, 32, 28);
                        card.setBackgroundColor(Color.WHITE);

                        LinearLayout.LayoutParams cardParams =
                                new LinearLayout.LayoutParams(
                                        ViewGroup.LayoutParams.MATCH_PARENT,
                                        ViewGroup.LayoutParams.WRAP_CONTENT
                                );

                        cardParams.setMargins(0, 0, 0, 20);
                        card.setLayoutParams(cardParams);
                        card.setElevation(8f);

                        TextView titleText = new TextView(this);
                        titleText.setText("🏠 " + propertyTitle);
                        titleText.setTextSize(20);
                        titleText.setTypeface(null, Typeface.BOLD);
                        titleText.setTextColor(Color.parseColor("#0F5F3A"));

                        TextView detailText = new TextView(this);
                        detailText.setText(
                                "👤 Name: " + name +
                                        "\n📞 Phone: " + phone +
                                        "\n📅 Move-in Date: " + moveInDate +
                                        "\n💬 Message: " + message +
                                        "\n\nStatus: " + status
                        );
                        detailText.setTextSize(15);
                        detailText.setTextColor(Color.parseColor("#333333"));
                        detailText.setPadding(0, 12, 0, 12);

                        Button btnApprove = new Button(this);
                        btnApprove.setText("Approve");
                        btnApprove.setTextColor(Color.WHITE);
                        btnApprove.setBackgroundColor(Color.parseColor("#1E7A5A"));

                        Button btnReject = new Button(this);
                        btnReject.setText("Reject");
                        btnReject.setTextColor(Color.WHITE);
                        btnReject.setBackgroundColor(Color.parseColor("#D32F2F"));

                        Button btnComplete = new Button(this);
                        btnComplete.setText("Complete Rental");
                        btnComplete.setTextColor(Color.parseColor("#0F5F3A"));
                        btnComplete.setBackgroundColor(Color.parseColor("#D9EDE4"));

                        btnApprove.setOnClickListener(v -> {

                            String propertyId = document.getString("propertyId");

                            document.getReference()
                                    .update("status", "Approved")
                                    .addOnSuccessListener(aVoid -> {

                                        if (propertyId != null && !propertyId.isEmpty()) {

                                            db.collection("properties")
                                                    .document(propertyId)
                                                    .update("status", "Reserved")
                                                    .addOnSuccessListener(unused -> {

                                                        Toast.makeText(
                                                                this,
                                                                "Request approved. Property marked as Reserved.",
                                                                Toast.LENGTH_LONG
                                                        ).show();

                                                        loadRequests();

                                                    });

                                        } else {
                                            Toast.makeText(this, "Request approved", Toast.LENGTH_SHORT).show();
                                            loadRequests();
                                        }

                                    });

                        });

                        btnReject.setOnClickListener(v -> {

                            String propertyId = document.getString("propertyId");

                            document.getReference()
                                    .update("status", "Rejected")
                                    .addOnSuccessListener(aVoid -> {

                                        if (propertyId != null && !propertyId.isEmpty()) {

                                            db.collection("properties")
                                                    .document(propertyId)
                                                    .update("status", "Available")
                                                    .addOnSuccessListener(unused -> {

                                                        Toast.makeText(
                                                                this,
                                                                "Request rejected. Property marked as Available.",
                                                                Toast.LENGTH_LONG
                                                        ).show();

                                                        loadRequests();

                                                    });

                                        } else {
                                            Toast.makeText(this, "Request rejected", Toast.LENGTH_SHORT).show();
                                            loadRequests();
                                        }

                                    });

                        });
                        btnComplete.setOnClickListener(v -> {

                            String propertyId = document.getString("propertyId");

                            document.getReference()
                                    .update("status", "Completed")
                                    .addOnSuccessListener(aVoid -> {

                                        if (propertyId != null && !propertyId.isEmpty()) {

                                            db.collection("properties")
                                                    .document(propertyId)
                                                    .update("status", "Rented")
                                                    .addOnSuccessListener(unused -> {

                                                        Toast.makeText(
                                                                this,
                                                                "Rental completed. Property marked as Rented.",
                                                                Toast.LENGTH_LONG
                                                        ).show();

                                                        loadRequests();

                                                    });

                                        } else {
                                            Toast.makeText(this, "Rental completed", Toast.LENGTH_SHORT).show();
                                            loadRequests();
                                        }

                                    });

                        });

                        card.addView(titleText);
                        card.addView(detailText);
                        card.addView(btnApprove);
                        card.addView(btnReject);
                        card.addView(btnComplete);

                        requestContainer.addView(card);
                    });

                })
                .addOnFailureListener(e ->
                        Toast.makeText(this, "Failed: " + e.getMessage(), Toast.LENGTH_LONG).show());
    }
}