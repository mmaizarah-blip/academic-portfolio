package com.example.smartrent.network;

import java.util.Map;

import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.http.FieldMap;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.POST;

public interface ToyyibPayApi {

    @FormUrlEncoded
    @POST("index.php/api/createBill")
    Call<ResponseBody> createBill(
            @FieldMap Map<String, String> fields
    );
}