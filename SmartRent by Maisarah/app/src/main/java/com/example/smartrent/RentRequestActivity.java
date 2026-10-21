package com.example.smartrent;

import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.firestore.FieldValue;
import com.google.firebase.firestore.FirebaseFirestore;

import java.util.HashMap;
import java.util.Map;

public class RentRequestActivity extends AppCompatActivity {

    EditText etRequestName, etRequestPhone, etMoveInDate, etRequestMessage;
    Button btnSubmitRequest;

    FirebaseFirestore db;
    FirebaseAuth mAuth;

    String propertyId = "";
    String propertyTitle = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_rent_request);

        etRequestName = findViewById(R.id.etRequestName);
        etRequestPhone = findViewById(R.id.etRequestPhone);
        etMoveInDate = findViewById(R.id.etMoveInDate);
        etRequestMessage = findViewById(R.id.etRequestMessage);
        btnSubmitRequest = findViewById(R.id.btnSubmitRequest);

        db = FirebaseFirestore.getInstance();
        mAuth = FirebaseAuth.getInstance();

        propertyId = getIntent().getStringExtra("propertyId");
        propertyTitle = getIntent().getStringExtra("propertyTitle");

        btnSubmitRequest.setOnClickListener(v -> submitRequest());
    }

    private void submitRequest() {
        String name = etRequestName.getText().toString();
        String phone = etRequestPhone.getText().toString();
        String moveInDate = etMoveInDate.getText().toString();
        String message = etRequestMessage.getText().toString();

        if (name.isEmpty() || phone.isEmpty() || moveInDate.isEmpty()) {
            Toast.makeText(this, "Please fill name, phone and move-in date", Toast.LENGTH_SHORT).show();
            return;
        }

        String userEmail = "";

        if (mAuth.getCurrentUser() != null) {
            userEmail = mAuth.getCurrentUser().getEmail();
        }

        Map<String, Object> request = new HashMap<>();
        request.put("propertyId", propertyId);
        request.put("propertyTitle", propertyTitle);
        request.put("userEmail", userEmail);
        request.put("name", name);
        request.put("phone", phone);
        request.put("moveInDate", moveInDate);
        request.put("message", message);
        request.put("status", "Pending");
        request.put("createdAt", FieldValue.serverTimestamp());

        db.collection("rent_requests")
                .add(request)
                .addOnSuccessListener(documentReference -> {
                    Toast.makeText(this, "Rental request submitted", Toast.LENGTH_SHORT).show();
                    finish();
                })
                .addOnFailureListener(e ->
                        Toast.makeText(this, "Failed: " + e.getMessage(), Toast.LENGTH_LONG).show());
    }
}