// lib/services/auth_service.dart

import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../constants/api_constants.dart';

class AuthService {
  final _storage = const FlutterSecureStorage();

  Future<bool> login(String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse(ApiConstants.loginUrl),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'email': email,
          'password': password,
          'device_name': 'flutter_app', 
        }),
      );

      if (response.statusCode == 200) {
        final body = jsonDecode(response.body);
        final token = body['token'] ?? body['access_token']; 
        
        if (token != null) {
          await _storage.write(key: 'auth_token', value: token);
          return true;
        }
        return false;
      } else {
        // Tratar erros de login (ex: credenciais inválidas)
        print('Erro de Login: ${response.body}');
        return false;
      }
    } catch (e) {
      print('Exceção durante o login: $e');
      return false;
    }
  }

  // Método de Logout
  Future<void> logout() async {
    // Em um cenário real, você chamaria o endpoint de logout do Laravel
    await _storage.delete(key: 'auth_token');
  }

  // Verificar se o usuário está logado
  Future<bool> isLoggedIn() async {
    return await _storage.read(key: 'auth_token') != null;
  }
  
  // Obter o token para uso em outras requisições
  Future<String?> getToken() async {
    return await _storage.read(key: 'auth_token');
  }
}
