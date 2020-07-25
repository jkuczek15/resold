import 'package:flutter/material.dart';
import 'package:resold/services/resold.dart';
import 'package:resold/models/product.dart';
import 'package:resold/builders/product-list-builder.dart';
import 'package:flappy_search_bar/flappy_search_bar.dart';
import 'package:geolocator/geolocator.dart';

class Home extends StatelessWidget {
  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
        title: 'Resold',
        theme: ThemeData(
            primarySwatch: const MaterialColor(0xff41b8ea, {
              50:  Color.fromRGBO(25,72,92, .1),
              100: Color.fromRGBO(25,72,92, .2),
              200: Color.fromRGBO(25,72,92, .3),
              300: Color.fromRGBO(25,72,92, .4),
              400: Color.fromRGBO(25,72,92, .5),
              500: Color.fromRGBO(25,72,92, .6),
              600: Color.fromRGBO(25,72,92, .7),
              700: Color.fromRGBO(25,72,92, .8),
              800: Color.fromRGBO(25,72,92, .9),
              900: Color.fromRGBO(25,72,92, 1)
            }),
            accentColor: Colors.white,
            primaryColor: const Color(0xff41b8ea)
        ),
        home: HomePage()
    );
  }
}

class HomePageState extends State<HomePage> {
  int selectedIndex = 0;

  Future<List<Product>> futureLocalProducts;
  Position currentLocation;

  final widgetOptions = [
    Text('Buy'),
    Text('Search'),
    Text('Sell'),
    Text('Orders'),
    Text('Account')
  ];

  @override
  void initState() {
    super.initState();

    Geolocator().getCurrentPosition(desiredAccuracy: LocationAccuracy.high).then((location) {
      setState(() {
        currentLocation = location;
        futureLocalProducts = Resold.fetchLocalProducts(location.latitude, location.longitude);
      });
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Row (
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Align (
                alignment: Alignment.centerLeft,
                child: Image.asset('assets/images/resold-white-logo.png', width: 145, height: 145)
            ),
            Align (
                alignment: Alignment.centerRight,
                child: Icon(Icons.message, color: Colors.white),
            )
          ],
        ),
        backgroundColor: const Color(0xff41b8ea),
      ),
      body: Center(
        child: getContent(),
      ),
      bottomNavigationBar: BottomNavigationBar(
        type: BottomNavigationBarType.fixed,
        items: <BottomNavigationBarItem>[
          BottomNavigationBarItem(icon: Icon(Icons.home), title: Text('Buy')),
          BottomNavigationBarItem(icon: Icon(Icons.search), title: Text('Search')),
          BottomNavigationBarItem(icon: Icon(Icons.attach_money), title: Text('Sell')),
          BottomNavigationBarItem(icon: Icon(Icons.receipt), title: Text('Orders')),
          BottomNavigationBarItem(icon: Icon(Icons.person), title: Text('Account')),
        ],
        currentIndex: selectedIndex,
        fixedColor: const Color(0xff41b8ea),
        unselectedItemColor: Colors.black,
        onTap: onItemTapped,
      ),
    );
  }

  Object getContent() {
    switch(selectedIndex) {
      case 0:
        return FutureBuilder<List<Product>>(
          future: futureLocalProducts,
          builder: (context, snapshot) {
            if (snapshot.hasData) {
              return ProductListBuilder.buildProductList(snapshot.data, currentLocation);
            } else if (snapshot.hasError) {
              return Text("${snapshot.error}");
            }
            // By default, show a loading spinner.
            return Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)));
          },
        );
      case 1:
        return
          SafeArea (
            child: SearchBar<Product>(
              hintText: 'Search entire marketplace here...',
              searchBarPadding: EdgeInsets.symmetric(horizontal: 20),
              cancellationWidget: Icon(Icons.cancel),
              onSearch: (term) => Resold.fetchSearchProducts(term, currentLocation.latitude, currentLocation.longitude),
              loader: Center(child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea))),
              onItemFound: (Product product, int index) {
                return ProductListBuilder.buildProductTile(currentLocation, product, index);
              },
            ),
          );
      default:
        return widgetOptions.elementAt(selectedIndex);
    }
  }

  void onItemTapped(int index) {
    setState(() {
      selectedIndex = index;
    });
  }
}

class HomePage extends StatefulWidget {
  HomePage({Key key}) : super(key: key);

  @override
  HomePageState createState() => HomePageState();
}