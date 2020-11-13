import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:resold/constants/ui-constants.dart';

class DropdownSizeList extends StatefulWidget {
  final DropdownSizeListState state = new DropdownSizeListState();

  DropdownSizeList({Key key}) : super(key: key);

  @override
  DropdownSizeListState createState() => state;
}

class DropdownSizeListState extends State<DropdownSizeList> {
  String sizeSelected;

  final List<String> sizes = [
    'Car',
    'Pickup Truck',
    'Delivery Van',
    'Moving Truck',
  ];

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return DropdownButtonFormField<String>(
      value: sizeSelected,
      icon: Icon(Icons.keyboard_arrow_down),
      iconSize: 20,
      elevation: 16,
      style: TextStyle(color: Colors.black),
      focusColor: ResoldBlue,
      hint: Text('Vehicle Required'),
      validator: (value) {
        if (value == null) {
          return 'Please select a vehicle.';
        }
        return null;
      },
      onChanged: (String newValue) {
        FocusScope.of(context).requestFocus(FocusNode());
        setState(() {
          sizeSelected = newValue;
        });
      },
      decoration: const InputDecoration(
        border: const OutlineInputBorder(),
      ),
      items: sizes.asMap().entries.map<DropdownMenuItem<String>>((entry) {
        return DropdownMenuItem<String>(
            value: getSelectedSizeId(entry.key).toString(),
            child: Container(
                width: 250,
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.start,
                  children: [
                    Icon(getSelectedSizeIcon(entry.key)),
                    SizedBox(width: 20),
                    Text(entry.value.toString()),
                  ],
                )));
      }).toList(),
    );
  }

  int getSelectedSizeId(int index) {
    switch (index) {
      case 0:
        // Small
        return 239;
      case 1:
        // Medium
        return 240;
      case 2:
        // Large
        return 241;
      case 3:
        // XLarge
        return 242;
      default:
        // Electronics
        return 240;
    }
  }

  IconData getSelectedSizeIcon(int index) {
    switch (index) {
      case 0:
        // Small
        return MdiIcons.carSide;
      case 1:
        // Medium
        return MdiIcons.carPickup;
      case 2:
        // Large
        return MdiIcons.vanUtility;
      case 3:
        // XLarge
        return MdiIcons.truck;
      default:
        // Medium
        return MdiIcons.carSide;
    }
  }
}
