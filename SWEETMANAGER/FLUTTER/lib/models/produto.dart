class Produto {
  final int id;
  final String nome;
  final double preco;

  Produto({
    required this.id,
    required this.nome,
    required this.preco,
  });

  factory Produto.fromJson(Map<String, dynamic> json) {
    return Produto(
      id: json['id'],
      nome: json['nome'],
      preco: double.parse(json['preco'].toString()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'nome': nome,
      'preco': preco.toString(),
    };
  }
}
