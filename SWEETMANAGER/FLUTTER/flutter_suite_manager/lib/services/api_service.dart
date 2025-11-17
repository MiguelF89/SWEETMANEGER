import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/instituicao.dart';
import '../models/produto.dart';
import '../models/venda.dart';
import 'auth_service.dart';
import '../constants/api_constants.dart';

class ApiService {
  final AuthService _authService = AuthService();

  Future<List<Instituicao>> getInstituicoes() async {
    final token = await _authService.getToken();
    final response = await http.get(
      Uri.parse(ApiConstants.instituicoesUrl),
      headers: {
        'Authorization': 'Bearer $token',
      },
    );
    if (response.statusCode == 200) {
      final List data = jsonDecode(response.body);
      return data.map((json) => Instituicao.fromJson(json)).toList();
    } else {
      throw Exception("Erro ao carregar instituições");
    }
  }

  Future<List<Produto>> getProdutos() async {
    final token = await _authService.getToken();
    final response = await http.get(
      Uri.parse(ApiConstants.produtosUrl),
      headers: {
        'Authorization': 'Bearer $token',
      },
    );
    if (response.statusCode == 200) {
      final List data = jsonDecode(response.body);
      return data.map((json) => Produto.fromJson(json)).toList();
    } else {
      throw Exception("Erro ao carregar produtos");
    }
  }

  Future<List<Venda>> getVendas() async {
    final token = await _authService.getToken();
    final response = await http.get(
      Uri.parse(ApiConstants.vendasUrl),
      headers: {
        'Authorization': 'Bearer $token',
      },
    );
    if (response.statusCode == 200) {
      final List data = jsonDecode(response.body);
      return data.map((json) => Venda.fromJson(json)).toList();
    } else {
      throw Exception("Erro ao carregar vendas");
    }
  }
}
