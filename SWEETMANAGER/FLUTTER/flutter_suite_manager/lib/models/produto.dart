

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
      id: json['id'] as int,
      nome: json['nome'] as String,

      preco: (json['preco'] is String) 
          ? double.parse(json['preco']) 
          : (json['preco'] as num).toDouble(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nome': nome,
      'preco': preco,
    };
  }
}
