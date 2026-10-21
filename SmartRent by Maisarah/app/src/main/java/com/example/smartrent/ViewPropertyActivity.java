package com.example.smartrent;

import android.content.Intent;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.bumptech.glide.Glide;
import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.firestore.FirebaseFirestore;
import com.google.firebase.firestore.Query;

import java.text.NumberFormat;
import java.util.Locale;

public class ViewPropertyActivity extends AppCompatActivity {

    LinearLayout listContainer;
    EditText etSearchProperty;
    Spinner spFilterType, spFilterStatus;
    Button btnApplyFilter, btnResetFilter;

    FirebaseFirestore db;

    double maxPrice = 0;
    boolean isStaff = false;

    String[] typeList = {
            "All",
            "Apartment",
            "Condominium",
            "Terrace House",
            "Semi-Detached",
            "Bungalow",
            "Townhouse",
            "Studio",
            "Villa"
    };

    String[] statusList = {
            "All",
            "Available",
            "Reserved",
            "Rented"
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_view_property);

        listContainer = findViewById(R.id.listContainer);
        etSearchProperty = findViewById(R.id.etSearchProperty);
        spFilterType = findViewById(R.id.spFilterType);
        spFilterStatus = findViewById(R.id.spFilterStatus);
        btnApplyFilter = findViewById(R.id.btnApplyFilter);
        btnResetFilter = findViewById(R.id.btnResetFilter);

        db = FirebaseFirestore.getInstance();

        if (FirebaseAuth.getInstance().getCurrentUser() != null) {
            String userEmail = FirebaseAuth.getInstance().getCurrentUser().getEmail();

            if (userEmail != null &&
                    userEmail.toLowerCase().endsWith("@landlord.smartrent.com.my")) {
                isStaff = true;
            }
        }

        maxPrice = getIntent().getDoubleExtra("maxPrice", 0);

        ArrayAdapter<String> typeAdapter =
                new ArrayAdapter<>(
                        this,
                        android.R.layout.simple_spinner_dropdown_item,
                        typeList
                );

        spFilterType.setAdapter(typeAdapter);

        ArrayAdapter<String> statusAdapter =
                new ArrayAdapter<>(
                        this,
                        android.R.layout.simple_spinner_dropdown_item,
                        statusList
                );

        spFilterStatus.setAdapter(statusAdapter);

        btnApplyFilter.setOnClickListener(v -> loadProperties());

        btnResetFilter.setOnClickListener(v -> {
            etSearchProperty.setText("");
            spFilterType.setSelection(0);
            spFilterStatus.setSelection(0);
            loadProperties();
        });

        loadProperties();
    }

    private void loadProperties() {

        Query query;

        String userEmail = "";

        if (FirebaseAuth.getInstance().getCurrentUser() != null) {
            userEmail = FirebaseAuth.getInstance().getCurrentUser().getEmail();
        }

        boolean isAdmin =
                userEmail != null &&
                        userEmail.endsWith("@admin.smartrent.com.my");

        boolean isLandlord =
                userEmail != null &&
                        userEmail.endsWith("@landlord.smartrent.com.my");

        if (maxPrice > 0) {

            if (isAdmin || isLandlord) {

                query = db.collection("properties")
                        .whereLessThanOrEqualTo("price", maxPrice);

            } else {

                query = db.collection("properties")
                        .whereEqualTo("approvalStatus", "Approved");

            }

        } else {

            if (isAdmin || isLandlord) {

                query = db.collection("properties");

            } else {

                query = db.collection("properties")
                        .whereEqualTo("approvalStatus", "Approved");

            }
        }

        query.get()
                .addOnSuccessListener(queryDocumentSnapshots -> {

                    listContainer.removeAllViews();

                    String searchText = etSearchProperty.getText().toString().toLowerCase().trim();
                    String selectedType = spFilterType.getSelectedItem().toString();
                    String selectedStatus = spFilterStatus.getSelectedItem().toString();

                    int shownCount = 0;

                    if (maxPrice > 0) {
                        TextView budgetInfo = new TextView(this);
                        budgetInfo.setText("Showing properties below RM " + String.format("%,.0f", maxPrice));
                        budgetInfo.setTextSize(15);
                        budgetInfo.setTextColor(Color.parseColor("#0F5F3A"));
                        budgetInfo.setPadding(0, 0, 0, 20);
                        listContainer.addView(budgetInfo);
                    }

                    for (int i = 0; i < queryDocumentSnapshots.size(); i++) {

                        var document = queryDocumentSnapshots.getDocuments().get(i);

                        String title = document.getString("title");
                        String location = document.getString("location");
                        String type = document.getString("type");
                        String description = document.getString("description");
                        String imageUrl = document.getString("imageUrl");
                        String status = document.getString("status");
                        String approvalStatus = document.getString("approvalStatus");
                        String paymentStatus = document.getString("paymentStatus");

                        if (approvalStatus == null) approvalStatus = "Pending";
                        if (paymentStatus == null) paymentStatus = "Pending";

                        if (title == null) title = "Untitled Property";
                        if (location == null) location = "No location";
                        if (type == null) type = "Property";
                        if (description == null) description = "No description available.";
                        if (status == null) status = "Available";

                        boolean matchesSearch =
                                searchText.isEmpty()
                                        || title.toLowerCase().contains(searchText)
                                        || location.toLowerCase().contains(searchText);

                        boolean matchesType =
                                selectedType.equals("All")
                                        || type.equalsIgnoreCase(selectedType);

                        boolean matchesStatus =
                                selectedStatus.equals("All")
                                        || status.equalsIgnoreCase(selectedStatus);

                        if (!matchesSearch || !matchesType || !matchesStatus) {
                            continue;
                        }

                        if (!isAdmin && !isLandlord && status.equalsIgnoreCase("Rented")) {
                            continue;
                        }

                        Object priceObj = document.get("price");
                        double price = 0.0;

                        if (priceObj instanceof Number) {
                            price = ((Number) priceObj).doubleValue();
                        } else if (priceObj instanceof String) {
                            try {
                                price = Double.parseDouble((String) priceObj);
                            } catch (NumberFormatException e) {
                                price = 0.0;
                            }
                        }
                        if (maxPrice > 0 && price > maxPrice) {
                            continue;
                        }

                        LinearLayout card = new LinearLayout(this);
                        card.setOrientation(LinearLayout.VERTICAL);
                        card.setPadding(40, 32, 40, 32);
                        card.setBackgroundResource(R.drawable.card_background);

                        LinearLayout.LayoutParams cardParams =
                                new LinearLayout.LayoutParams(
                                        ViewGroup.LayoutParams.MATCH_PARENT,
                                        ViewGroup.LayoutParams.WRAP_CONTENT
                                );

                        cardParams.setMargins(0, 0, 0, 24);
                        card.setLayoutParams(cardParams);
                        card.setElevation(12f);

                        ImageView propertyImage = new ImageView(this);

                        LinearLayout.LayoutParams imageParams =
                                new LinearLayout.LayoutParams(
                                        ViewGroup.LayoutParams.MATCH_PARENT,
                                        380
                                );

                        imageParams.setMargins(0, 0, 0, 20);
                        propertyImage.setLayoutParams(imageParams);
                        propertyImage.setScaleType(ImageView.ScaleType.CENTER_CROP);
                        propertyImage.setBackgroundColor(Color.parseColor("#D9EDE4"));

                        if (imageUrl != null && !imageUrl.isEmpty()) {
                            Glide.with(ViewPropertyActivity.this)
                                    .load(imageUrl)
                                    .into(propertyImage);
                        }

                        TextView statusText = new TextView(this);
                        statusText.setText("● " + status);
                        statusText.setTextSize(14);
                        statusText.setTypeface(null, Typeface.BOLD);
                        statusText.setTextColor(Color.parseColor("#0F5F3A"));
                        statusText.setPadding(0, 0, 0, 8);
                        if (status.equalsIgnoreCase("Available")) {
                            statusText.setTextColor(Color.parseColor("#1E7A5A"));
                        } else if (status.equalsIgnoreCase("Reserved")) {
                            statusText.setTextColor(Color.parseColor("#F9A825"));
                        } else if (status.equalsIgnoreCase("Rented")) {
                            statusText.setTextColor(Color.parseColor("#D32F2F"));
                        }

                        TextView approvalText = new TextView(this);
                        approvalText.setText("Approval: " + approvalStatus + " | Payment: " + paymentStatus);
                        approvalText.setTextSize(14);
                        approvalText.setTypeface(null, Typeface.BOLD);
                        approvalText.setTextColor(Color.parseColor("#555555"));
                        approvalText.setPadding(0, 0, 0, 8);

                        TextView titleText = new TextView(this);
                        titleText.setText("🏠 " + title);
                        titleText.setTextSize(22);
                        titleText.setTypeface(null, Typeface.BOLD);
                        titleText.setTextColor(Color.parseColor("#0F5F3A"));

                        TextView locationText = new TextView(this);
                        locationText.setText("📍 " + location);
                        locationText.setTextSize(15);
                        locationText.setTextColor(Color.parseColor("#666666"));
                        locationText.setPadding(0, 8, 0, 0);

                        TextView priceText = new TextView(this);
                        NumberFormat formatter = NumberFormat.getNumberInstance(Locale.US);
                        priceText.setText("RM " + formatter.format(price));
                        priceText.setTextSize(24);
                        priceText.setTypeface(null, Typeface.BOLD);
                        priceText.setTextColor(Color.parseColor("#1E7A5A"));
                        priceText.setPadding(0, 14, 0, 0);

                        TextView detailText = new TextView(this);
                        detailText.setText("Type: " + type + "\n\n" + description + "\n\nTap to view full details →");
                        detailText.setTextSize(14);
                        detailText.setTextColor(Color.parseColor("#777777"));
                        detailText.setPadding(0, 10, 0, 16);

                        Button btnDelete = new Button(this);
                        btnDelete.setText("Delete Listing");
                        btnDelete.setTextColor(Color.parseColor("#0F5F3A"));
                        btnDelete.setBackgroundColor(Color.parseColor("#D9EDE4"));

                        btnDelete.setOnClickListener(v -> {
                            document.getReference()
                                    .delete()
                                    .addOnSuccessListener(aVoid -> {
                                        Toast.makeText(
                                                ViewPropertyActivity.this,
                                                "Property Deleted",
                                                Toast.LENGTH_SHORT
                                        ).show();

                                        loadProperties();
                                    });
                        });

                        card.addView(propertyImage);
                        card.addView(statusText);
                        if (isStaff) {
                            card.addView(approvalText);
                        }
                        card.addView(titleText);
                        card.addView(locationText);
                        card.addView(priceText);
                        card.addView(detailText);

                        if (isStaff) {
                            card.addView(btnDelete);
                        }

                        card.setOnClickListener(v -> {
                            Intent intent = new Intent(ViewPropertyActivity.this, PropertyDetailActivity.class);
                            intent.putExtra("propertyId", document.getId());
                            startActivity(intent);
                        });

                        listContainer.addView(card);
                        shownCount++;
                    }

                    if (shownCount == 0) {
                        TextView emptyText = new TextView(this);
                        emptyText.setText("No properties found.");
                        emptyText.setTextSize(18);
                        emptyText.setTextColor(Color.parseColor("#777777"));
                        listContainer.addView(emptyText);
                    }

                })
                .addOnFailureListener(e -> {
                    Toast.makeText(this,
                            "Failed: " + e.getMessage(),
                            Toast.LENGTH_LONG).show();
                });
    }
}