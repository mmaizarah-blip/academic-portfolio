package com.example.smartrent;

import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.Spinner;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.bumptech.glide.Glide;
import com.google.firebase.firestore.FirebaseFirestore;
import com.google.firebase.storage.FirebaseStorage;
import com.google.firebase.storage.StorageReference;

import java.util.HashMap;
import java.util.Map;
import java.util.UUID;

public class EditPropertyActivity extends AppCompatActivity {

    EditText etTitle, etLocation, etPrice, etDescription;
    EditText etBedrooms, etBathrooms, etBuiltUpArea, etParking;
    EditText etAgentName, etAgentPhone, etTourUrl;

    Spinner spType, spStatus;
    Button btnUpdateProperty, btnChangeImage;
    ImageView imgEditProperty;

    FirebaseFirestore db;
    StorageReference storageRef;

    String propertyId;
    Uri imageUri;
    String imageUrl = "";

    String[] propertyTypes = {
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
            "Available",
            "Reserved",
            "Rented"
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_edit_property);

        db = FirebaseFirestore.getInstance();
        storageRef = FirebaseStorage.getInstance().getReference("property_images");

        propertyId = getIntent().getStringExtra("propertyId");

        if (propertyId == null) {
            Toast.makeText(this, "Property not found", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        imgEditProperty = findViewById(R.id.imgEditProperty);
        btnChangeImage = findViewById(R.id.btnChangeImage);

        etTitle = findViewById(R.id.etEditTitle);
        etLocation = findViewById(R.id.etEditLocation);
        etPrice = findViewById(R.id.etEditPrice);
        etDescription = findViewById(R.id.etEditDescription);
        etBedrooms = findViewById(R.id.etEditBedrooms);
        etBathrooms = findViewById(R.id.etEditBathrooms);
        etBuiltUpArea = findViewById(R.id.etEditBuiltUpArea);
        etParking = findViewById(R.id.etEditParking);
        etAgentName = findViewById(R.id.etEditAgentName);
        etAgentPhone = findViewById(R.id.etEditAgentPhone);
        etTourUrl = findViewById(R.id.etEditTourUrl);

        spType = findViewById(R.id.spEditType);
        spStatus = findViewById(R.id.spEditStatus);
        btnUpdateProperty = findViewById(R.id.btnUpdateProperty);

        ArrayAdapter<String> typeAdapter =
                new ArrayAdapter<>(
                        this,
                        android.R.layout.simple_spinner_dropdown_item,
                        propertyTypes
                );

        spType.setAdapter(typeAdapter);

        ArrayAdapter<String> statusAdapter =
                new ArrayAdapter<>(
                        this,
                        android.R.layout.simple_spinner_dropdown_item,
                        statusList
                );

        spStatus.setAdapter(statusAdapter);

        btnChangeImage.setOnClickListener(v -> openGallery());

        btnUpdateProperty.setOnClickListener(v -> {
            if (imageUri != null) {
                uploadNewImageThenUpdateProperty();
            } else {
                updateProperty();
            }
        });

        loadProperty();
    }

    private void openGallery() {
        Intent intent = new Intent(Intent.ACTION_GET_CONTENT);
        intent.setType("image/*");
        startActivityForResult(intent, 100);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == 100 && resultCode == RESULT_OK && data != null) {
            imageUri = data.getData();
            imgEditProperty.setImageURI(imageUri);
        }
    }

    private void loadProperty() {
        db.collection("properties")
                .document(propertyId)
                .get()
                .addOnSuccessListener(document -> {

                    etTitle.setText(document.getString("title"));
                    etLocation.setText(document.getString("location"));
                    etDescription.setText(document.getString("description"));
                    etBedrooms.setText(document.getString("bedrooms"));
                    etBathrooms.setText(document.getString("bathrooms"));
                    etBuiltUpArea.setText(document.getString("builtUpArea"));
                    etParking.setText(document.getString("parking"));
                    etAgentName.setText(document.getString("agentName"));
                    etAgentPhone.setText(document.getString("agentPhone"));
                    etTourUrl.setText(document.getString("tourUrl"));

                    imageUrl = document.getString("imageUrl");

                    if (imageUrl != null && !imageUrl.isEmpty()) {
                        Glide.with(EditPropertyActivity.this)
                                .load(imageUrl)
                                .into(imgEditProperty);
                    }

                    Object priceObj = document.get("price");

                    if (priceObj instanceof Number) {
                        etPrice.setText(String.format("%.0f", ((Number) priceObj).doubleValue()));
                    } else if (priceObj instanceof String) {
                        etPrice.setText((String) priceObj);
                    }

                    String type = document.getString("type");
                    String status = document.getString("status");

                    setSpinnerSelection(spType, propertyTypes, type);
                    setSpinnerSelection(spStatus, statusList, status);

                })
                .addOnFailureListener(e ->
                        Toast.makeText(this, "Failed: " + e.getMessage(), Toast.LENGTH_LONG).show());
    }

    private void uploadNewImageThenUpdateProperty() {
        String fileName = UUID.randomUUID().toString() + ".jpg";
        StorageReference imageRef = storageRef.child(fileName);

        imageRef.putFile(imageUri)
                .addOnSuccessListener(taskSnapshot ->
                        imageRef.getDownloadUrl().addOnSuccessListener(uri -> {
                            imageUrl = uri.toString();
                            updateProperty();
                        }))
                .addOnFailureListener(e ->
                        Toast.makeText(this, "Image upload failed: " + e.getMessage(), Toast.LENGTH_LONG).show());
    }

    private void updateProperty() {
        String title = etTitle.getText().toString();
        String location = etLocation.getText().toString();
        String priceText = etPrice.getText().toString()
                .replace(",", "")
                .replace("RM", "")
                .trim();

        String type = spType.getSelectedItem().toString();
        String description = etDescription.getText().toString();
        String bedrooms = etBedrooms.getText().toString();
        String bathrooms = etBathrooms.getText().toString();
        String builtUpArea = etBuiltUpArea.getText().toString();
        String parking = etParking.getText().toString();
        String agentName = etAgentName.getText().toString();
        String agentPhone = etAgentPhone.getText().toString();
        String status = spStatus.getSelectedItem().toString();
        String tourUrl = etTourUrl.getText().toString();

        if (title.isEmpty() || location.isEmpty() || priceText.isEmpty()
                || description.isEmpty() || bedrooms.isEmpty()
                || bathrooms.isEmpty() || builtUpArea.isEmpty()
                || parking.isEmpty() || agentName.isEmpty()
                || agentPhone.isEmpty()) {

            Toast.makeText(this, "Please fill all required fields", Toast.LENGTH_SHORT).show();
            return;
        }

        double price;

        try {
            price = Double.parseDouble(priceText);
        } catch (NumberFormatException e) {
            Toast.makeText(this, "Please enter valid price", Toast.LENGTH_SHORT).show();
            return;
        }

        Map<String, Object> property = new HashMap<>();
        property.put("title", title);
        property.put("location", location);
        property.put("price", price);
        property.put("type", type);
        property.put("description", description);
        property.put("bedrooms", bedrooms);
        property.put("bathrooms", bathrooms);
        property.put("builtUpArea", builtUpArea);
        property.put("parking", parking);
        property.put("agentName", agentName);
        property.put("agentPhone", agentPhone);
        property.put("status", status);
        property.put("tourUrl", tourUrl);
        property.put("imageUrl", imageUrl);

        db.collection("properties")
                .document(propertyId)
                .update(property)
                .addOnSuccessListener(aVoid -> {
                    Toast.makeText(this, "Property updated successfully", Toast.LENGTH_SHORT).show();
                    finish();
                })
                .addOnFailureListener(e ->
                        Toast.makeText(this, "Failed: " + e.getMessage(), Toast.LENGTH_LONG).show());
    }

    private void setSpinnerSelection(Spinner spinner, String[] list, String value) {
        if (value == null) return;

        for (int i = 0; i < list.length; i++) {
            if (list[i].equalsIgnoreCase(value)) {
                spinner.setSelection(i);
                return;
            }
        }
    }
}