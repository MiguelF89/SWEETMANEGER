import 'package:flutter/material.dart';
import '../models/instituicao.dart';
import '../services/api_service.dart';

class InstituicoesListScreen extends StatefulWidget {
  const InstituicoesListScreen({super.key});

  @override
  State<InstituicoesListScreen> createState() => _InstituicoesListScreenState();
}

class _InstituicoesListScreenState extends State<InstituicoesListScreen> {
  final ApiService _apiService = ApiService();
  late Future<List<Instituicao>> _futureInstituicoes;

  @override
  void initState() {
    super.initState();
    _futureInstituicoes = _apiService.getInstituicoes();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Instituições'),
        backgroundColor: Colors.blue,
      ),
      body: FutureBuilder<List<Instituicao>>(
        future: _futureInstituicoes,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError) {
            return Center(
              child: Text(
                "Erro: ${snapshot.error}",
                style: const TextStyle(color: Colors.red),
              ),
            );
          }

          final instituicoes = snapshot.data ?? [];

          if (instituicoes.isEmpty) {
            return const Center(
              child: Text('Nenhuma instituição encontrada.'),
            );
          }

          return ListView.builder(
            itemCount: instituicoes.length,
            itemBuilder: (context, index) {
              final inst = instituicoes[index];

              return Card(
                margin: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                elevation: 3,
                child: ListTile(
                  title: Text(inst.nome),
                  subtitle: Text("CNPJ: ${inst.cnpj}"),
                  trailing: const Icon(Icons.arrow_forward_ios, size: 16),
                  onTap: () {
                    // Futuro: tela de detalhes / edição
                  },
                ),
              );
            },
          );
        },
      ),
    );
  }
}
