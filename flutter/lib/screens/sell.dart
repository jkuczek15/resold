import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:resold/widgets/image-uploader.dart';
import 'package:resold/widgets/scroll-column-expandable.dart';
import 'package:resold/widgets/scrollable-category-list.dart';

class SellPage extends StatefulWidget {
  SellPage({Key key}) : super(key: key);

  @override
  SellPageState createState() => SellPageState();
}

class SellPageState extends State<SellPage> {

  final List<bool> localGlobalSelected = [false, false];
  String condition;

  @override
  Widget build(BuildContext context) {
    return Padding (
      padding: EdgeInsets.all(20),
      child: ScrollColumnExpandable(
        mainAxisAlignment: MainAxisAlignment.start,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          ImageUploader(),
          SizedBox(height: 20),
          TextField(
            decoration: InputDecoration(
              border: OutlineInputBorder(),
              labelText: 'Listing Title'
            ),
          ),
          SizedBox(height: 20),
          TextField(
            decoration: InputDecoration(
                border: OutlineInputBorder(),
                labelText: 'Title Description'
            ),
          ),
          SizedBox(height: 20),
          ScrollableCategoryList(),
          SizedBox(height: 20),
          Row (
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Container (
                  width: 100,
                  child: TextField(
                    keyboardType: TextInputType.number,
                    decoration: InputDecoration(
                        border: OutlineInputBorder(),
                        labelText: 'Price'
                    ),
                  )
              ),
              ToggleButtons(
                children: [
                  Padding(padding: EdgeInsets.fromLTRB(25, 10, 25, 10), child: Column (
                      children: [
                        Icon(Icons.local_shipping, semanticLabel: 'Delivery'),
                        Text('Delivery')
                      ],
                    )
                  ),
                  Padding(padding: EdgeInsets.fromLTRB(25, 10, 25, 10), child: Column (
                      children: [
                        Icon(Icons.location_on, semanticLabel: 'Pickup'),
                        Text('Pickup')
                      ]
                    )
                  ),
                ],
                onPressed: (int index) {
                  setState(() {
                    localGlobalSelected[index] = !localGlobalSelected[index];
                  });
                },
                isSelected: localGlobalSelected,
              ),
            ]
          ),
          SizedBox(height: 20),
          Container (
            width: 600,
            child: DropdownButtonFormField<String>(
              value: condition,
              iconSize: 0,
              elevation: 16,
              style: TextStyle(color: Colors.black),
              focusColor: const Color(0xff41b8ea),
              hint: Text('Condition'),
              onChanged: (String newValue) {
                setState(() {
                  condition = newValue;
                });
              },
              decoration: const InputDecoration(
                border: const OutlineInputBorder(),
              ),
              items: <String>['New', 'Like New', 'Good', 'Used'].map<DropdownMenuItem<String>>((String value) {
                return DropdownMenuItem<String>(
                  value: value,
                  child: Text(value),
                );
              }).toList(),
            ),
          ),
          SizedBox(height: 20),
          TextField(
            maxLines: null,
            minLines: null,
            keyboardType: TextInputType.multiline,
            decoration: InputDecoration(
              border: OutlineInputBorder(),
              labelText: 'Details',
            ),
          ),
          SizedBox(height: 20),
          ButtonTheme (
            minWidth: 340.0,
            height: 70.0,
            child: RaisedButton(
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadiusDirectional.circular(8)
              ),
              onPressed: () async {
                // show a loading indicator
                showDialog(
                  context: context,
                  builder: (BuildContext context) {
                    return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
                  }
                );
                Navigator.of(context, rootNavigator: true).pop('dialog');
              },
              child: Text('Post',
                style: new TextStyle(
                  fontSize: 20.0,
                  fontWeight: FontWeight.bold,
                  color: Colors.white
                )
              ),
              color: Colors.black,
              textColor: Colors.white,
            )
        )
      ]
      )
    );
  }
}
