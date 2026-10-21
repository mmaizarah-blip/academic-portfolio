package com.example.smartrent;

import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
import java.text.NumberFormat;
import java.util.Locale;
import android.view.View;
import com.google.firebase.auth.FirebaseAuth;
import androidx.viewpager2.widget.ViewPager2;
import java.util.ArrayList;
import java.util.List;
import android.graphics.Color;
import android.widget.LinearLayout;
import java.util.HashMap;
import java.util.Map;

import androidx.appcompat.app.AppCompatActivity;

import com.google.firebase.firestore.FirebaseFirestore;

public class PropertyDetailActivity extends AppCompatActivity {

    ViewPager2 viewPagerImages;
    TextView tvTitleDetail, tvLocationDetail, tvPriceDetail, tvDescriptionDetail,  btnBack, btnFavourite, tvStatusDetail, tvBedrooms, tvBathrooms, tvArea, tvParking, tvAgentName, tvAgentPhone;
    Button btnRentNow, btnVirtualTour, btnShareProperty;
    Button btnEditProperty;
    LinearLayout layoutDots;

    FirebaseFirestore db;

    String agentPhone = "";
    String tourUrl = "";
    boolean isStaff = false;
    String propertyId;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_property_detail);

        viewPagerImages = findViewById(R.id.viewPagerImages);
        tvTitleDetail = findViewById(R.id.tvTitleDetail);
        tvLocationDetail = findViewById(R.id.tvLocationDetail);
        tvPriceDetail = findViewById(R.id.tvPriceDetail);
        tvDescriptionDetail = findViewById(R.id.tvDescriptionDetail);
        btnRentNow = findViewById(R.id.btnRentNow);
        btnVirtualTour = findViewById(R.id.btnVirtualTour);
        btnEditProperty = findViewById(R.id.btnEditProperty);
        btnBack = findViewById(R.id.btnBack);
        tvStatusDetail = findViewById(R.id.tvStatusDetail);
        tvBedrooms = findViewById(R.id.tvBedrooms);
        tvBathrooms = findViewById(R.id.tvBathrooms);
        tvArea = findViewById(R.id.tvArea);
        tvParking = findViewById(R.id.tvParking);
        tvAgentName = findViewById(R.id.tvAgentName);
        tvAgentPhone = findViewById(R.id.tvAgentPhone);
        layoutDots = findViewById(R.id.layoutDots);
        btnShareProperty = findViewById(R.id.btnShareProperty);
        btnFavourite = findViewById(R.id.btnFavourite);

        btnBack.setOnClickListener(v -> finish());

        db = FirebaseFirestore.getInstance();

        propertyId = getIntent().getStringExtra("propertyId");

        if (propertyId == null) {
            Toast.makeText(this, "Property not found", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        if (FirebaseAuth.getInstance().getCurrentUser() != null) {
            String userEmail = FirebaseAuth.getInstance().getCurrentUser().getEmail();

            if (userEmail != null &&
                    userEmail.toLowerCase().endsWith("@landlord.smartrent.com.my")) {
                isStaff = true;
            }
        }

        if (isStaff) {
            btnEditProperty.setVisibility(View.VISIBLE);
        } else {
            btnEditProperty.setVisibility(View.GONE);
        }

        btnEditProperty.setOnClickListener(v -> {
            Intent intent = new Intent(PropertyDetailActivity.this, EditPropertyActivity.class);
            intent.putExtra("propertyId", propertyId);
            startActivity(intent);
        });

        loadPropertyDetails(propertyId);
    }

    private void loadPropertyDetails(String propertyId) {
        db.collection("properties")
                .document(propertyId)
                .get()
                .addOnSuccessListener(document -> {

                    String title = document.getString("title");
                    String location = document.getString("location");
                    String type = document.getString("type");
                    String description = document.getString("description");
                    String imageUrl = document.getString("imageUrl");
                    ArrayList<String> imageList = new ArrayList<>();

                    Object imagesObj = document.get("images");

                    if (imagesObj instanceof List<?>) {
                        List<?> list = (List<?>) imagesObj;

                        for (Object item : list) {
                            if (item instanceof String) {
                                imageList.add((String) item);
                            }
                        }
                    }
                    String status = document.getString("status");
                    String bedrooms = document.getString("bedrooms");
                    String bathrooms = document.getString("bathrooms");
                    String builtUpArea = document.getString("builtUpArea");
                    String parking = document.getString("parking");
                    String agentName = document.getString("agentName");

                    agentPhone = document.getString("agentPhone");
                    tourUrl = document.getString("tourUrl");

                    Object priceObj = document.get("price");
                    double price = 0.0;

                    if (priceObj instanceof Number) {
                        price = ((Number) priceObj).doubleValue();
                    }

                    if (title == null) title = "Untitled Property";
                    if (location == null) location = "No location";
                    if (type == null) type = "Property";
                    if (description == null) description = "No description available.";
                    if (agentPhone == null || agentPhone.isEmpty()) agentPhone = "0123456789";
                    if (tourUrl == null || tourUrl.isEmpty()) tourUrl = "";
                    if (status == null || status.isEmpty()) status = "Available";
                    if (bedrooms == null || bedrooms.isEmpty()) bedrooms = "4";
                    if (bathrooms == null || bathrooms.isEmpty()) bathrooms = "3";
                    if (builtUpArea == null || builtUpArea.isEmpty()) builtUpArea = "2200";
                    if (parking == null || parking.isEmpty()) parking = "2";
                    if (agentName == null || agentName.isEmpty()) agentName = "Ali Rahman";

                    tvTitleDetail.setText(title);
                    tvLocationDetail.setText("📍 " + location);
                    NumberFormat formatter = NumberFormat.getNumberInstance(Locale.US);
                    tvPriceDetail.setText("RM " + formatter.format(price));
                    tvStatusDetail.setText(status);
                    if (status.equalsIgnoreCase("Available")) {
                        tvStatusDetail.setTextColor(Color.parseColor("#0F5F3A"));
                        btnRentNow.setVisibility(View.VISIBLE);
                    } else if (status.equalsIgnoreCase("Reserved")) {
                        tvStatusDetail.setTextColor(Color.parseColor("#F9A825"));
                        btnRentNow.setVisibility(View.GONE);
                    } else if (status.equalsIgnoreCase("Rented")) {
                        tvStatusDetail.setTextColor(Color.parseColor("#D32F2F"));
                        btnRentNow.setVisibility(View.GONE);
                    }

                    tvBedrooms.setText("🛏\n" + bedrooms + "\nBedrooms");
                    tvBathrooms.setText("🛁\n" + bathrooms + "\nBathrooms");
                    tvArea.setText("📐\n" + builtUpArea + "\nsq.ft");
                    tvParking.setText("🚗\n" + parking + "\nParking");

                    tvDescriptionDetail.setText(description);
                    tvAgentName.setText(agentName);
                    tvAgentPhone.setText("☎ " + agentPhone);
                    if (imageList.isEmpty() && imageUrl != null && !imageUrl.isEmpty()) {
                        imageList.add(imageUrl);
                    }

                    if (!imageList.isEmpty()) {

                        PropertyImageAdapter adapter =
                                new PropertyImageAdapter(imageList);

                        viewPagerImages.setAdapter(adapter);

                        createDots(imageList.size(), 0);

                        viewPagerImages.registerOnPageChangeCallback(
                                new ViewPager2.OnPageChangeCallback() {

                                    @Override
                                    public void onPageSelected(int position) {
                                        super.onPageSelected(position);
                                        createDots(imageList.size(), position);
                                    }
                                });
                    }
                    String finalTitle = title;
                    String finalLocation = location;
                    String finalImageUrl = imageUrl;
                    double finalPrice = price;

                    btnFavourite.setOnClickListener(v -> {

                        String userEmail = "";

                        if (FirebaseAuth.getInstance().getCurrentUser() != null) {
                            userEmail = FirebaseAuth.getInstance().getCurrentUser().getEmail();
                        }

                        if (userEmail == null || userEmail.isEmpty()) {
                            Toast.makeText(this, "Please login first", Toast.LENGTH_SHORT).show();
                            return;
                        }

                        Map<String, Object> favourite = new HashMap<>();
                        favourite.put("userEmail", userEmail);
                        favourite.put("propertyId", propertyId);
                        favourite.put("title", finalTitle);
                        favourite.put("location", finalLocation);
                        favourite.put("price", finalPrice);
                        favourite.put("imageUrl", finalImageUrl);

                        db.collection("favourites")
                                .whereEqualTo("userEmail", userEmail)
                                .whereEqualTo("propertyId", propertyId)
                                .get()
                                .addOnSuccessListener(queryDocumentSnapshots -> {

                                    if (!queryDocumentSnapshots.isEmpty()) {
                                        Toast.makeText(this, "Already in favourites", Toast.LENGTH_SHORT).show();
                                        btnFavourite.setText("♥");
                                        return;
                                    }

                                    db.collection("favourites")
                                            .add(favourite)
                                            .addOnSuccessListener(documentReference -> {
                                                btnFavourite.setText("♥");
                                                Toast.makeText(this, "Added to favourites", Toast.LENGTH_SHORT).show();
                                            });
                                });
                    });

                    String finalPriceText = "RM " + formatter.format(price);

                    btnRentNow.setOnClickListener(v -> {
                        Intent intent = new Intent(PropertyDetailActivity.this, RentRequestActivity.class);
                        intent.putExtra("propertyId", propertyId);
                        intent.putExtra("propertyTitle", finalTitle);
                        startActivity(intent);
                    });

                    btnVirtualTour.setOnClickListener(v -> {
                        if (tourUrl.isEmpty()) {
                            Toast.makeText(this, "Virtual tour not available", Toast.LENGTH_SHORT).show();
                        } else {
                            Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(tourUrl));
                            startActivity(intent);
                        }
                    });

                    btnShareProperty.setOnClickListener(v -> {

                        String shareText =
                                "Check out this property on SmartRent!\n\n" +
                                        finalTitle + "\n" +
                                        finalLocation + "\n" +
                                        finalPriceText;

                        Intent shareIntent = new Intent(Intent.ACTION_SEND);
                        shareIntent.setType("text/plain");
                        shareIntent.putExtra(Intent.EXTRA_TEXT, shareText);

                        startActivity(Intent.createChooser(shareIntent, "Share Property"));
                    });

                })
                .addOnFailureListener(e ->
                        Toast.makeText(this, "Failed: " + e.getMessage(), Toast.LENGTH_LONG).show());
    }
    private void createDots(int count, int currentPosition) {

        layoutDots.removeAllViews();

        for (int i = 0; i < count; i++) {

            TextView dot = new TextView(this);

            if (i == currentPosition) {
                dot.setText("●");
            } else {
                dot.setText("○");
            }

            dot.setTextSize(18);
            dot.setPadding(6,0,6,0);

            layoutDots.addView(dot);
        }
    }

    @Override
    protected void onResume() {
        super.onResume();

        if (propertyId != null) {
            loadPropertyDetails(propertyId);
        }
    }
}