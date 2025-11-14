import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/instituicao.dart';
import '../models/produto.dart';
import '../models/venda.dart';

class ApiService {
  // O IP 10.0.2.2 é o endereço do host (sua máquina) quando rodando no emulador Android.
  // Para rodar no navegador, use 'localhost' ou o IP da sua máquina.
  // Como estamos no sandbox, vamos usar o endereço do servidor Laravel.
  // O servidor Laravel está rodando em http://127.0.0.1:8000.
  // Para acessar de fora, precisamos expor a porta.
  // No entanto, para fins de demonstração do código, vou usar um placeholder.
  // O usuário precisará ajustar isso para o ambiente real.
  static const String _baseUrl = 'http://127.0.0.1:8000/api';
  // static const String _baseUrl = 'http://<SEU_IP_AQUI>:8000/api'; // Para teste real

  // Token de autenticação (simulação, o ideal é implementar login)
  // O usuário precisará obter um token Sanctum para testar.
  static const String _token = 'SEU_TOKEN_SANCTUM_AQUI'; 

  static Map<String, String> get _headers => {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $_token',
      };

  // --- Instituições ---

  Future<List<Instituicao>> fetchInstituicoes() async {
    final response = await http.get(Uri.parse('$_baseUrl/instituicoes'), headers: _headers);

    if (response.statusCode == 200) {
      List jsonResponse = json.decode(response.body);
      return jsonResponse.map((data) => Instituicao.fromJson(data)).toList();
    } else {
      throw Exception('Falha ao carregar instituições');
    }
  }

  // --- Produtos ---

  Future<List<Produto>> fetchProdutos() async {
    final response = await http.get(Uri.parse('$_baseUrl/produtos'), headers: _headers);

    if (response.statusCode == 200) {
      List jsonResponse = json.decode(response.body);
      return jsonResponse.map((data) => Produto.fromJson(data)).toList();
    } else {
      throw Exception('Falha ao carregar produtos');
    }
  }

  // --- Vendas ---

  Future<List<Venda>> fetchVendas() async {
    final response = await http.get(Uri.parse('$_baseUrl/vendas'), headers: _headers);

    if (response.statusCode == 200) {
      List jsonResponse = json.decode(response.body);
      return jsonResponse.map((data) => Venda.fromJson(data)).toList();
    } else {
      throw Exception('Falha ao carregar vendas');
    }
  }
  
  // Métodos de CRUD (apenas um exemplo de criação)
  Future<Venda> createVenda(Venda venda) async {
    final response = await http.post(
      Uri.parse('$_baseUrl/vendas'),
      headers: _headers,
      body: json.encode(venda.toJson()),
    );

    if (response.statusCode == 201) {
      return Venda.fromJson(json.decode(response.body));
    } else {
      throw Exception('Falha ao criar venda: ${response.body}');
    }
  }
}
