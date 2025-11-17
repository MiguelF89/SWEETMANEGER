
class Instituicao {
  final int id;
  final String nome;
  final String cnpj;
  final String contato;


  Instituicao({
    required this.id,
    required this.nome,
    required this.cnpj,
    required this.contato,
  });

  factory Instituicao.fromJson(Map<String, dynamic> json) {
    return Instituicao(
      id: json['id'] as int,
      nome: json['nome'] as String,
      cnpj: json['cnpj'] as String,
      contato: json['contato'] as String,

    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nome': nome,
      'cnpj': cnpj,
      'contato': contato,
    };
  }
}
