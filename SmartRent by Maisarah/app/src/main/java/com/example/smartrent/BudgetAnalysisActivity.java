package com.example.smartrent;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

public class BudgetAnalysisActivity extends AppCompatActivity {

    EditText etSalary, etCommitment;
    Button btnCalculate, btnViewSuitable;
    TextView tvResult;

    double suggestedHousePrice = 0;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_budget_analysis);

        etSalary = findViewById(R.id.etSalary);
        etCommitment = findViewById(R.id.etCommitment);
        btnCalculate = findViewById(R.id.btnCalculate);
        btnViewSuitable = findViewById(R.id.btnViewSuitable);
        tvResult = findViewById(R.id.tvResult);

        btnCalculate.setOnClickListener(v -> calculateBudget());

        btnViewSuitable.setOnClickListener(v -> {
            Intent intent = new Intent(BudgetAnalysisActivity.this, ViewPropertyActivity.class);
            intent.putExtra("maxPrice", suggestedHousePrice);
            startActivity(intent);
        });
    }

    private void calculateBudget() {
        String salaryText = etSalary.getText().toString();
        String commitmentText = etCommitment.getText().toString();

        if (salaryText.isEmpty() || commitmentText.isEmpty()) {
            Toast.makeText(this, "Please enter salary and commitment", Toast.LENGTH_SHORT).show();
            return;
        }

        double salary = Double.parseDouble(salaryText);
        double commitment = Double.parseDouble(commitmentText);

        double availableIncome = salary - commitment;
        double affordableMonthlyPayment = availableIncome * 0.35;
        suggestedHousePrice = affordableMonthlyPayment * 200;

        tvResult.setText(
                "Available Income: RM " + String.format("%,.0f", availableIncome) +
                        "\n\nAffordable Monthly Payment: RM " + String.format("%,.0f", affordableMonthlyPayment) +
                        "\n\nSuggested House Price: RM " + String.format("%,.0f", suggestedHousePrice)
        );

        btnViewSuitable.setVisibility(View.VISIBLE);
    }
}