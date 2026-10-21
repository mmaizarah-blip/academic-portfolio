package com.example.smartrent;

import android.os.Bundle;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.google.firebase.firestore.FirebaseFirestore;
import com.github.mikephil.charting.charts.PieChart;
import com.github.mikephil.charting.data.PieData;
import com.github.mikephil.charting.data.PieDataSet;
import com.github.mikephil.charting.data.PieEntry;
import android.graphics.Color;
import com.github.mikephil.charting.charts.BarChart;
import com.github.mikephil.charting.data.BarData;
import com.github.mikephil.charting.data.BarDataSet;
import com.github.mikephil.charting.data.BarEntry;
import com.github.mikephil.charting.components.XAxis;
import com.github.mikephil.charting.formatter.IndexAxisValueFormatter;
import java.util.ArrayList;

public class ReportActivity extends AppCompatActivity {

    TextView tvTotalProperties, tvAvailableProperties, tvRentedProperties,
            tvReservedProperties, tvRentalRate;
    TextView tvPendingApproval, tvApprovedListings, tvRejectedListings, tvPendingPayments, tvPaidPayments, tvRevenue;
    PieChart pieChartStatus;
    BarChart barChartType, barChartRequest;
    FirebaseFirestore db;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_report);

        tvTotalProperties = findViewById(R.id.tvTotalProperties);
        tvAvailableProperties = findViewById(R.id.tvAvailableProperties);
        tvRentedProperties = findViewById(R.id.tvRentedProperties);
        tvReservedProperties = findViewById(R.id.tvReservedProperties);
        tvRentalRate = findViewById(R.id.tvRentalRate);
        pieChartStatus = findViewById(R.id.pieChartStatus);
        barChartType = findViewById(R.id.barChartType);
        barChartRequest = findViewById(R.id.barChartRequest);
        tvPendingApproval = findViewById(R.id.tvPendingApproval);
        tvApprovedListings = findViewById(R.id.tvApprovedListings);
        tvRejectedListings = findViewById(R.id.tvRejectedListings);

        tvPendingPayments = findViewById(R.id.tvPendingPayments);
        tvPaidPayments = findViewById(R.id.tvPaidPayments);

        tvRevenue = findViewById(R.id.tvRevenue);

        db = FirebaseFirestore.getInstance();

        loadReport();
        loadRequestReport();
    }

    private void loadReport() {
        db.collection("properties")
                .get()
                .addOnSuccessListener(queryDocumentSnapshots -> {

                    int total = 0;
                    int available = 0;
                    int rented = 0;
                    int reserved = 0;
                    int apartment = 0, condominium = 0, terrace = 0, semiD = 0;
                    int bungalow = 0, townhouse = 0, studio = 0, villa = 0;
                    int pendingApproval = 0;
                    int approvedListings = 0;
                    int rejectedListings = 0;

                    int pendingPayments = 0;
                    int paidPayments = 0;

                    double totalRevenue = 0;

                    for (int i = 0; i < queryDocumentSnapshots.size(); i++) {

                        String status =
                                queryDocumentSnapshots
                                        .getDocuments()
                                        .get(i)
                                        .getString("status");

                        total++;

                        if (status != null) {
                            if (status.equalsIgnoreCase("Available")) {
                                available++;
                            } else if (status.equalsIgnoreCase("Rented")) {
                                rented++;
                            } else if (status.equalsIgnoreCase("Reserved")) {
                                reserved++;
                            }
                        }

                        String approvalStatus =
                                queryDocumentSnapshots
                                        .getDocuments()
                                        .get(i)
                                        .getString("approvalStatus");

                        String paymentStatus =
                                queryDocumentSnapshots
                                        .getDocuments()
                                        .get(i)
                                        .getString("paymentStatus");
                        if (approvalStatus != null) {

                            if (approvalStatus.equalsIgnoreCase("Pending")) {
                                pendingApproval++;
                            }

                            else if (approvalStatus.equalsIgnoreCase("Approved")) {
                                approvedListings++;
                            }

                            else if (approvalStatus.equalsIgnoreCase("Rejected")) {
                                rejectedListings++;
                            }
                        }
                        Object paymentObj =
                                queryDocumentSnapshots
                                        .getDocuments()
                                        .get(i)
                                        .get("paymentAmount");

                        double paymentAmount = 0;

                        if (paymentObj instanceof Number) {
                            paymentAmount = ((Number) paymentObj).doubleValue();
                        }

                        if (paymentStatus != null) {

                            if (paymentStatus.equalsIgnoreCase("Pending")) {
                                pendingPayments++;
                            }

                            else if (paymentStatus.equalsIgnoreCase("Paid")) {

                                paidPayments++;

                                totalRevenue += paymentAmount;
                            }
                        }

                        String type = queryDocumentSnapshots
                                .getDocuments()
                                .get(i)
                                .getString("type");

                        if (type != null) {
                            if (type.equalsIgnoreCase("Apartment")) apartment++;
                            else if (type.equalsIgnoreCase("Condominium")) condominium++;
                            else if (type.equalsIgnoreCase("Terrace House")) terrace++;
                            else if (type.equalsIgnoreCase("Semi-Detached")) semiD++;
                            else if (type.equalsIgnoreCase("Bungalow")) bungalow++;
                            else if (type.equalsIgnoreCase("Townhouse")) townhouse++;
                            else if (type.equalsIgnoreCase("Studio")) studio++;
                            else if (type.equalsIgnoreCase("Villa")) villa++;
                        }
                    }

                    double rentalRate = 0;

                    if (total > 0) {
                        rentalRate = ((double) rented / total) * 100;
                    }

                    tvTotalProperties.setText("🏠 Total Properties: " + total);
                    tvAvailableProperties.setText("🟢 Available Properties: " + available);
                    tvRentedProperties.setText("🔴 Rented Properties: " + rented);
                    tvReservedProperties.setText("🟡 Reserved Properties: " + reserved);
                    tvRentalRate.setText("📊 Rental Rate: " + String.format("%.1f", rentalRate) + "%");
                    tvPendingApproval.setText(
                            "⏳ Pending Approval: " + pendingApproval);

                    tvApprovedListings.setText(
                            "✅ Approved Listings: " + approvedListings);

                    tvRejectedListings.setText(
                            "❌ Rejected Listings: " + rejectedListings);

                    tvPendingPayments.setText(
                            "💳 Pending Payments: " + pendingPayments);

                    tvPaidPayments.setText(
                            "💰 Paid Payments: " + paidPayments);

                    tvRevenue.setText(
                            "💵 Total Revenue: RM "
                                    + String.format("%.2f", totalRevenue));

                    showPieChart(available, rented, reserved);
                    showTypeBarChart(apartment, condominium, terrace, semiD, bungalow, townhouse, studio, villa);

                })
                .addOnFailureListener(e ->
                        Toast.makeText(this,
                                "Failed: " + e.getMessage(),
                                Toast.LENGTH_LONG).show());
    }

    private void loadRequestReport() {
        db.collection("rent_requests")
                .get()
                .addOnSuccessListener(queryDocumentSnapshots -> {

                    int pending = 0;
                    int approved = 0;
                    int rejected = 0;
                    int completed = 0;

                    for (int i = 0; i < queryDocumentSnapshots.size(); i++) {

                        String status =
                                queryDocumentSnapshots
                                        .getDocuments()
                                        .get(i)
                                        .getString("status");

                        if (status != null) {
                            if (status.equalsIgnoreCase("Pending")) {
                                pending++;
                            } else if (status.equalsIgnoreCase("Approved")) {
                                approved++;
                            } else if (status.equalsIgnoreCase("Rejected")) {
                                rejected++;
                            } else if (status.equalsIgnoreCase("Completed")) {
                                completed++;
                            }
                        }
                    }

                    showRequestBarChart(pending, approved, rejected, completed);

                })
                .addOnFailureListener(e ->
                        Toast.makeText(this,
                                "Failed to load rental requests: " + e.getMessage(),
                                Toast.LENGTH_LONG).show());
    }
    private void showPieChart(int available, int rented, int reserved) {

        ArrayList<PieEntry> entries = new ArrayList<>();

        if (available > 0) {
            entries.add(new PieEntry(available, "Available"));
        }

        if (rented > 0) {
            entries.add(new PieEntry(rented, "Rented"));
        }

        if (reserved > 0) {
            entries.add(new PieEntry(reserved, "Reserved"));
        }

        PieDataSet dataSet =
                new PieDataSet(entries, "Property Status");

        ArrayList<Integer> colors = new ArrayList<>();
        colors.add(Color.parseColor("#1E7A5A"));
        colors.add(Color.parseColor("#D32F2F"));
        colors.add(Color.parseColor("#FBC02D"));

        dataSet.setColors(colors);
        dataSet.setValueTextSize(14f);
        dataSet.setValueTextColor(Color.WHITE);

        PieData data = new PieData(dataSet);

        pieChartStatus.setData(data);
        pieChartStatus.setUsePercentValues(false);
        pieChartStatus.setDrawHoleEnabled(true);
        pieChartStatus.setHoleRadius(45f);
        pieChartStatus.setTransparentCircleRadius(50f);
        pieChartStatus.setCenterText("Property\nStatus");
        pieChartStatus.setCenterTextSize(16f);
        pieChartStatus.getDescription().setEnabled(false);
        pieChartStatus.animateY(1000);
        pieChartStatus.invalidate();
    }
    private void showTypeBarChart(
            int apartment,
            int condominium,
            int terrace,
            int semiD,
            int bungalow,
            int townhouse,
            int studio,
            int villa) {

        ArrayList<BarEntry> entries = new ArrayList<>();

        entries.add(new BarEntry(0, apartment));
        entries.add(new BarEntry(1, condominium));
        entries.add(new BarEntry(2, terrace));
        entries.add(new BarEntry(3, semiD));
        entries.add(new BarEntry(4, bungalow));
        entries.add(new BarEntry(5, townhouse));
        entries.add(new BarEntry(6, studio));
        entries.add(new BarEntry(7, villa));

        BarDataSet dataSet = new BarDataSet(entries, "Property Type");
        dataSet.setColor(Color.parseColor("#1E7A5A"));
        dataSet.setValueTextSize(12f);
        dataSet.setValueTextColor(Color.BLACK);

        BarData data = new BarData(dataSet);
        data.setBarWidth(0.6f);

        String[] labels = {
                "Apartment",
                "Condo",
                "Terrace",
                "Semi-D",
                "Bungalow",
                "Townhouse",
                "Studio",
                "Villa"
        };

        XAxis xAxis = barChartType.getXAxis();
        xAxis.setValueFormatter(new IndexAxisValueFormatter(labels));
        xAxis.setPosition(XAxis.XAxisPosition.BOTTOM);
        xAxis.setGranularity(1f);
        xAxis.setLabelRotationAngle(-35);
        xAxis.setDrawGridLines(false);

        barChartType.setData(data);
        barChartType.getDescription().setEnabled(false);
        barChartType.getAxisRight().setEnabled(false);
        barChartType.animateY(1000);
        barChartType.invalidate();
    }
    private void showRequestBarChart(
            int pending,
            int approved,
            int rejected,
            int completed) {

        ArrayList<BarEntry> entries = new ArrayList<>();

        entries.add(new BarEntry(0, pending));
        entries.add(new BarEntry(1, approved));
        entries.add(new BarEntry(2, rejected));
        entries.add(new BarEntry(3, completed));

        BarDataSet dataSet = new BarDataSet(entries, "Rental Requests");
        dataSet.setColor(Color.parseColor("#0F5F3A"));
        dataSet.setValueTextSize(12f);
        dataSet.setValueTextColor(Color.BLACK);

        BarData data = new BarData(dataSet);
        data.setBarWidth(0.6f);

        String[] labels = {
                "Pending",
                "Approved",
                "Rejected",
                "Completed"
        };

        XAxis xAxis = barChartRequest.getXAxis();
        xAxis.setValueFormatter(new IndexAxisValueFormatter(labels));
        xAxis.setPosition(XAxis.XAxisPosition.BOTTOM);
        xAxis.setGranularity(1f);
        xAxis.setLabelRotationAngle(-25);
        xAxis.setDrawGridLines(false);

        barChartRequest.setData(data);
        barChartRequest.getDescription().setEnabled(false);
        barChartRequest.getAxisRight().setEnabled(false);
        barChartRequest.animateY(1000);
        barChartRequest.invalidate();
    }
}