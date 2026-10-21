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

import com.example.smartrent.network.ToyyibPayApi;
import com.example.smartrent.network.ToyyibPayClient;
import com.example.smartrent.utils.Constants;
import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.firestore.FirebaseFirestore;
import com.google.firebase.storage.FirebaseStorage;
import com.google.firebase.storage.StorageReference;

import org.json.JSONArray;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;
import java.util.UUID;

import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class AddPropertyActivity extends AppCompatActivity {

    EditText etTitle, etLocation, etPrice, etDescription;
    EditText etBedrooms, etBathrooms, etBuiltUpArea, etParking;
    EditText etAgentName, etAgentPhone, etTourUrl;

    Spinner spType, spStatus;
    Button btnSaveProperty, btnSelectImage;
    ImageView imgProperty;

    FirebaseFirestore db;
    StorageReference storageRef;

    ArrayList<Uri> imageUris = new ArrayList<>();
    ArrayList<String> imageUrls = new ArrayList<>();

    String[] propertyTypes = {
            "Apartment", "Condominium", "Terrace House", "Semi-Detached",
            "Bungalow", "Townhouse", "Studio", "Villa"
    };

    String[] statusList = {
            "Available", "Reserved", "Rented"
    };

    String title, location, priceText, type, status, description;
    String bedrooms, bathrooms, builtUpArea, parking, agentName, agentPhone, tourUrl;
    double propertyPrice;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_add_property);

        etTitle = findViewById(R.id.etTitle);
        etLocation = findViewById(R.id.etLocation);
        etPrice = findViewById(R.id.etPrice);
        etDescription = findViewById(R.id.etDescription);
        etBedrooms = findViewById(R.id.etBedrooms);
        etBathrooms = findViewById(R.id.etBathrooms);
        etBuiltUpArea = findViewById(R.id.etBuiltUpArea);
        etParking = findViewById(R.id.etParking);
        etAgentName = findViewById(R.id.etAgentName);
        etAgentPhone = findViewById(R.id.etAgentPhone);
        etTourUrl = findViewById(R.id.etTourUrl);

        spType = findViewById(R.id.spType);
        spStatus = findViewById(R.id.spStatus);

        btnSaveProperty = findViewById(R.id.btnSaveProperty);
        btnSelectImage = findViewById(R.id.btnSelectImage);
        imgProperty = findViewById(R.id.imgProperty);

        db = FirebaseFirestore.getInstance();
        storageRef = FirebaseStorage.getInstance().getReference("property_images");

        spType.setAdapter(new ArrayAdapter<>(
                this,
                android.R.layout.simple_spinner_dropdown_item,
                propertyTypes
        ));

        spStatus.setAdapter(new ArrayAdapter<>(
                this,
                android.R.layout.simple_spinner_dropdown_item,
                statusList
        ));

        btnSelectImage.setOnClickListener(v -> openGallery());

        btnSaveProperty.setOnClickListener(v -> {

            if (!validateForm()) {
                return;
            }

            if (imageUris.isEmpty()) {
                Toast.makeText(this, "Please select at least one property image", Toast.LENGTH_SHORT).show();
                return;
            }

            btnSaveProperty.setEnabled(false);
            btnSaveProperty.setText("Saving...");

            imageUrls.clear();
            uploadImagesThenSaveProperty(0);
        });
    }

    private boolean validateForm() {
        title = etTitle.getText().toString().trim();
        location = etLocation.getText().toString().trim();
        priceText = etPrice.getText().toString()
                .replace(",", "")
                .replace("RM", "")
                .trim();

        type = spType.getSelectedItem().toString();
        status = spStatus.getSelectedItem().toString();

        description = etDescription.getText().toString().trim();
        bedrooms = etBedrooms.getText().toString().trim();
        bathrooms = etBathrooms.getText().toString().trim();
        builtUpArea = etBuiltUpArea.getText().toString().trim();
        parking = etParking.getText().toString().trim();
        agentName = etAgentName.getText().toString().trim();
        agentPhone = etAgentPhone.getText().toString().trim();
        tourUrl = etTourUrl.getText().toString().trim();

        if (title.isEmpty() || location.isEmpty() || priceText.isEmpty()
                || description.isEmpty() || bedrooms.isEmpty()
                || bathrooms.isEmpty() || builtUpArea.isEmpty()
                || parking.isEmpty() || agentName.isEmpty()
                || agentPhone.isEmpty()) {

            Toast.makeText(this, "Please fill all required fields", Toast.LENGTH_SHORT).show();
            return false;
        }

        try {
            propertyPrice = Double.parseDouble(priceText);
        } catch (NumberFormatException e) {
            Toast.makeText(this, "Please enter valid price, example: 280000", Toast.LENGTH_SHORT).show();
            return false;
        }

        return true;
    }

    private void openGallery() {
        Intent intent = new Intent(Intent.ACTION_OPEN_DOCUMENT);
        intent.setType("image/*");
        intent.putExtra(Intent.EXTRA_ALLOW_MULTIPLE, true);
        intent.addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION);
        intent.addFlags(Intent.FLAG_GRANT_PERSISTABLE_URI_PERMISSION);
        startActivityForResult(intent, 100);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == 100 && resultCode == RESULT_OK && data != null) {

            imageUris.clear();

            if (data.getClipData() != null) {
                int count = data.getClipData().getItemCount();

                for (int i = 0; i < count; i++) {
                    Uri uri = data.getClipData().getItemAt(i).getUri();

                    try {
                        getContentResolver().takePersistableUriPermission(
                                uri,
                                Intent.FLAG_GRANT_READ_URI_PERMISSION
                        );
                    } catch (Exception ignored) {}

                    imageUris.add(uri);
                }

                imgProperty.setImageURI(imageUris.get(0));
                Toast.makeText(this, count + " images selected", Toast.LENGTH_SHORT).show();

            } else if (data.getData() != null) {
                Uri uri = data.getData();

                try {
                    getContentResolver().takePersistableUriPermission(
                            uri,
                            Intent.FLAG_GRANT_READ_URI_PERMISSION
                    );
                } catch (Exception ignored) {}

                imageUris.add(uri);
                imgProperty.setImageURI(uri);
                Toast.makeText(this, "1 image selected", Toast.LENGTH_SHORT).show();
            }
        }
    }

    private void uploadImagesThenSaveProperty(int index) {
        if (index >= imageUris.size()) {
            saveProperty();
            return;
        }

        Toast.makeText(this, "Uploading image " + (index + 1), Toast.LENGTH_SHORT).show();

        String fileName = UUID.randomUUID().toString() + ".jpg";
        StorageReference imageRef = storageRef.child(fileName);

        imageRef.putFile(imageUris.get(index))
                .addOnSuccessListener(taskSnapshot ->
                        imageRef.getDownloadUrl().addOnSuccessListener(uri -> {
                            imageUrls.add(uri.toString());
                            uploadImagesThenSaveProperty(index + 1);
                        }))
                .addOnFailureListener(e -> {
                    btnSaveProperty.setEnabled(true);
                    btnSaveProperty.setText("Save Property");
                    Toast.makeText(this, "Image upload failed: " + e.getMessage(), Toast.LENGTH_LONG).show();
                });
    }

    private void saveProperty() {
        String mainImageUrl = "";

        if (!imageUrls.isEmpty()) {
            mainImageUrl = imageUrls.get(0);
        }

        int paymentAmount = imageUrls.size() * 5;

        Map<String, Object> property = new HashMap<>();
        property.put("title", title);
        property.put("location", location);
        property.put("price", propertyPrice);
        property.put("type", type);
        property.put("description", description);
        property.put("bedrooms", bedrooms);
        property.put("bathrooms", bathrooms);
        property.put("builtUpArea", builtUpArea);
        property.put("parking", parking);
        property.put("agentName", agentName);
        property.put("agentPhone", agentPhone);
        property.put("tourUrl", tourUrl);
        property.put("status", status);

        property.put("approvalStatus", "Pending");
        property.put("paymentStatus", "Pending");
        property.put("paymentAmount", paymentAmount);

        property.put("imageUrl", mainImageUrl);
        property.put("images", imageUrls);

        if (FirebaseAuth.getInstance().getCurrentUser() != null) {
            property.put("landlordEmail",
                    FirebaseAuth.getInstance().getCurrentUser().getEmail());
        }

        db.collection("properties")
                .add(property)
                .addOnSuccessListener(documentReference -> {
                    Toast.makeText(this, "Property saved. Creating payment bill...", Toast.LENGTH_SHORT).show();

                    createToyyibPayBill(
                            documentReference.getId(),
                            title,
                            paymentAmount
                    );
                })
                .addOnFailureListener(e -> {
                    btnSaveProperty.setEnabled(true);
                    btnSaveProperty.setText("Save Property");
                    Toast.makeText(this, "Failed to save property: " + e.getMessage(), Toast.LENGTH_LONG).show();
                });
    }

    private void createToyyibPayBill(String propertyId, String propertyTitle, int amountRM) {
        String userEmail = "";

        if (FirebaseAuth.getInstance().getCurrentUser() != null) {
            userEmail = FirebaseAuth.getInstance().getCurrentUser().getEmail();
        }

        Map<String, String> bill = new HashMap<>();
        bill.put("userSecretKey", Constants.TOYYIBPAY_SECRET_KEY);
        bill.put("categoryCode", Constants.TOYYIBPAY_CATEGORY_CODE);
        bill.put("billName", "SmartRent Listing Fee");
        bill.put("billDescription", "Listing payment for " + propertyTitle);
        bill.put("billPriceSetting", "1");
        bill.put("billPayorInfo", "1");
        bill.put("billAmount", String.valueOf(amountRM * 100));
        bill.put("billReturnUrl", "https://toyyibpay.com");
        bill.put("billCallbackUrl", "https://toyyibpay.com");
        bill.put("billExternalReferenceNo", propertyId);
        bill.put("billTo", "SmartRent Landlord");
        bill.put("billEmail", userEmail);
        bill.put("billPhone", "0123456789");

        ToyyibPayApi api = ToyyibPayClient
                .getClient()
                .create(ToyyibPayApi.class);

        api.createBill(bill).enqueue(new Callback<ResponseBody>() {
            @Override
            public void onResponse(Call<ResponseBody> call, Response<ResponseBody> response) {
                try {
                    if (response.body() == null) {
                        btnSaveProperty.setEnabled(true);
                        btnSaveProperty.setText("Save Property");
                        Toast.makeText(AddPropertyActivity.this,
                                "ToyyibPay response empty",
                                Toast.LENGTH_LONG).show();
                        return;
                    }

                    String result = response.body().string();
                    JSONArray array = new JSONArray(result);

                    String billCode = array
                            .getJSONObject(0)
                            .getString("BillCode");

                    db.collection("properties")
                            .document(propertyId)
                            .update("toyyibpayBillCode", billCode);

                    String paymentUrl = "https://toyyibpay.com/" + billCode;

                    Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(paymentUrl));
                    startActivity(intent);

                    finish();

                } catch (Exception e) {
                    btnSaveProperty.setEnabled(true);
                    btnSaveProperty.setText("Save Property");
                    Toast.makeText(AddPropertyActivity.this,
                            "ToyyibPay error: " + e.getMessage(),
                            Toast.LENGTH_LONG).show();
                }
            }

            @Override
            public void onFailure(Call<ResponseBody> call, Throwable t) {
                btnSaveProperty.setEnabled(true);
                btnSaveProperty.setText("Save Property");
                Toast.makeText(AddPropertyActivity.this,
                        "ToyyibPay failed: " + t.getMessage(),
                        Toast.LENGTH_LONG).show();
            }
        });
    }
}