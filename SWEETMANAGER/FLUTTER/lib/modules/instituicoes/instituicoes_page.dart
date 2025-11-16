import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class InstituicoesPage extends StatelessWidget {
  const InstituicoesPage({super.key});

  Future<List<dynamic>> fetchInstituicoes() async {
    final response = await http.get(
      Uri.parse('http://10.0.2.2:8000/api/instituicoes'),
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception("Erro ao carregar instituições");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Instituições")),

      body: FutureBuilder(
        future: fetchInstituicoes(),

        builder: (context, snapshot) {

          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError) {
            return const Center(child: Text("Erro ao carregar dados"));
          }


          final data = snapshot.data as List;

          return ListView.builder(
            itemCount: data.length,
            itemBuilder: (context, index) {
              final inst = data[index];

              return ListTile(
                title: Text(inst["nome"]),
                subtitle: Text("CNPJ: ${inst["cnpj"]}"),
                trailing: const Icon(Icons.chevron_right),
              );
            },
          );
        },
      ),
    );
  }
}
