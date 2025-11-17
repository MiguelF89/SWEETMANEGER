import 'package:flutter/material.dart';
import '../models/instituicao.dart';
import '../services/api_service.dart';

class InstituicoesScreen extends StatefulWidget {
  @override
  _InstituicoesScreenState createState() => _InstituicoesScreenState();
}

class _InstituicoesScreenState extends State<InstituicoesScreen> {
  final ApiService _apiService = ApiService();
  late Future<List<Instituicao>> futureInstituicoes;

  @override
  void initState() {
    super.initState();
    futureInstituicoes = _apiService.getInstituicoes();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("Gerenciar Instituições")),
      body: FutureBuilder<List<Instituicao>>(
        future: futureInstituicoes,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: CircularProgressIndicator());
          }

          if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return Center(child: Text("Nenhuma instituição encontrada"));
          }

          final instituicoes = snapshot.data!;
          print(instituicoes);

          return ListView.builder(
            itemCount: instituicoes.length,
            itemBuilder: (context, index) {
              final inst = instituicoes[index];

              return ListTile(
                title: Text('texto${inst.nome}'),
                subtitle: Text('texto${inst.cnpj}'),
                onTap: () {},
              );
            },
          );
        },
      ),
    );
  }
}
