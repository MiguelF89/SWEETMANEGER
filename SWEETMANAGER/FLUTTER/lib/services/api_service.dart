import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;

class ApiService {
  final String baseUrl = "http://SEU_IP/api";

  Future<Map<String, String>> getHeaders() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    String? token = prefs.getString("token");

    return {
      "Accept": "application/json",
      "Authorization": "Bearer $token",
    };
  }

  Future<http.Response> get(String endpoint) async {
    return http.get(
      Uri.parse("$baseUrl/$endpoint"),
      headers: await getHeaders(),
    );
  }
}
