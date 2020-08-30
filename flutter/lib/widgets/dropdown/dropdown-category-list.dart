import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';

class DropdownCategoryList extends StatefulWidget {

  final DropdownCategoryListState state = new DropdownCategoryListState();

  DropdownCategoryList({Key key}) : super(key: key);

  @override
  DropdownCategoryListState createState() => state;
}

class DropdownCategoryListState extends State<DropdownCategoryList> {

  String categorySelected;

  final List<String> categories = [
    'Electronics',
    'Fashion',
    'Home & Lawn',
    'Outdoors',
    'Sporting Goods',
    'Music',
    'Collectibles',
    'Handmade'
  ];

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return DropdownButtonFormField<String>(
      value: categorySelected,
      icon: Icon(Icons.keyboard_arrow_down),
      iconSize: 20,
      elevation: 16,
      style: TextStyle(color: Colors.black),
      focusColor: const Color(0xff41b8ea),
      hint: Text('Category'),
      onChanged: (String newValue) {
        FocusScope.of(context).requestFocus(FocusNode());
        setState(() {
          categorySelected = newValue;
        });
      },
      decoration: const InputDecoration(
        border: const OutlineInputBorder(),
      ),
      items: categories.asMap().entries.map<DropdownMenuItem<String>>((entry) {
        return DropdownMenuItem<String>(
          value: getSelectedCategoryId(entry.key).toString(),
          child: Container (
            width: 250,
            child: Row (
              mainAxisAlignment: MainAxisAlignment.start,
              children: [
                Icon(getSelectedCategoryIcon(entry.key)),
                SizedBox(width: 20),
                Text(entry.value.toString()),
              ],
            )
          )
        );
      }).toList(),
    );
  }

  int getSelectedCategoryId(int index) {
    switch (index) {
      case 0:
      // Electronics
        return 42;
      case 1:
      // Fashion
        return 93;
      case 2:
      // Home and Lawn
        return 100;
      case 3:
      // Outdoors
        return 101;
      case 4:
      // Sporting Goods
        return 102;
      case 5:
      // Music
        return 103;
      case 6:
      // Collectibles
        return 104;
      case 7:
      // Handmade
        return 105;
      default:
      // Electronics
        return 42;
    }
  }

  IconData getSelectedCategoryIcon(int index) {
    switch (index) {
      case 0:
      // Electronics
        return Icons.computer;
      case 1:
      // Fashion
        return MdiIcons.tshirtCrew;
      case 2:
      // Home and Lawn
        return MdiIcons.sofa;
      case 3:
      // Outdoors
        return Icons.directions_bike;
      case 4:
      // Sporting Goods
        return MdiIcons.basketball;
      case 5:
      // Music
        return MdiIcons.guitarAcoustic;
      case 6:
      // Collectibles
        return MdiIcons.cards;
      case 7:
      // Handmade
        return MdiIcons.handHeart;
      default:
      // Electronics
        return Icons.computer;
    }
  }
}
