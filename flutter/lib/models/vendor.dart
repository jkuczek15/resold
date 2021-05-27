import 'package:resold/constants/url-config.dart';

class Vendor {
  final int id;
  final String name;
  final String about;
  final String profilePicture;

  Vendor({this.id, this.name, this.about, this.profilePicture});

  String getImagePath() {
    return baseImagePath + '/' + profilePicture + '?d=' + DateTime.now().millisecond.toString();
  }

  factory Vendor.fromJson(dynamic doc) {
    try {
      return Vendor(
        id: int.tryParse(doc['id']),
        name: doc['name'].toString().trim(),
        about: doc['about'].toString(),
        profilePicture: doc['profilePicture'].toString(),
      );
    } catch (exception) {
      return Vendor();
    }
  } // end function fromJson
}
