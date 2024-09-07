<a id="readme-top"></a>



<!-- PROJECT SHIELDS -->
[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]



<!-- PROJECT LOGO -->
<br />
<div align="center">
<h3 align="center">CSV State Parser</h3>

  <p align="center">
    Parses CSV files with a simple finite-state machine.
  </p>
</div>



<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li><a href="#getting-started">Getting Started</a></li>
    <li><a href="#installation">Installation</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>


<!-- GETTING STARTED -->

## Installation

Installation is simple.

Just run `composer require tii/csv-state-parser`

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- USAGE EXAMPLES -->

## Usage

Create a Parser class that extends `\Tii\CsvStateParser\CsvStateParser` and implement the `stateStart` and `result`
methods. See below for a full example.

Every row gets passed into the current actives stateMethod (`stateStart` in the beginning).

You can change the state from the next row on by calling `$this->state($nextState)` and pass a String backed enum or a
value that can be cast to string. The next row will get passed to the corresponding stateMethod. I.e. if you call
`$this->state('fooBar')` the next row will get passed to `stateFooBar(array $row)`.

If you need to convert **every** field in the CSV file beforehand you can overwrite the
`protected function mapValue(string $value): string` method. This is i.e.
useful for encoding conversions.

There are a few additional helpers that help you achieve stuff:

| method                    | description                                                                  |
|---------------------------|------------------------------------------------------------------------------|
| `$this->done();`          | Tell the parser to quit early.                                               |
| `$this->skip(int $count)` | Tell the parser to skip `$count` lines before calling the stateMethod again. |

You are responsible for compiling the data that the parser should return at the end yourself. This data should be
returned by the
`result()` method.

Here is a full example:

```php
/**
 * @extends \Tii\CsvStateParser\CsvStateParser<array<int, int>> 
 */
class SumParser extends \Tii\CsvStateParser\CsvStateParser
{

    protected array $sums = [];

    protected function result(): array
    {
        return $this->sums;
    }
    
    protected function stateStart(array $row): void
    {
        if ($row[0] === 'START') {
            $this->state('list');
        }
    }
    
    protected function stateList(array $row): void
    {
        if ($row[0] === 'END') {
            $this->done();
            return;
        }
        
        $this->items[] = array_reduce($row, fn($sum, $number) => $sum + $number, 0);
    }

}
```

You can use your parser by instantiating it, and calling the `parse(string $filename)` method. \
If you need to adjust the separator, enclosure and escape char you can pass those to the constructor.

⚠️ **Beware!** In contrast to PHPs fgetcsv function this package uses ';' as the default separator character for CSV
files.

```php
$parser = new SumParser(separator: ',');
$list = $parser->parse('filename.csv');
```

<p align="right">(<a href="#readme-top">back to top</a>)</p>





<!-- CONTRIBUTING -->

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any
contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also
simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">(<a href="#readme-top">back to top</a>)</p>

### Top contributors:

<a href="https://github.com/TiiFuchs/csv-state-parser/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=TiiFuchs/csv-state-parser" alt="contrib.rocks image" />
</a>



<!-- LICENSE -->

## License

Distributed under the MIT License. See `LICENSE` for more information.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- CONTACT -->

## Contact

Tii - [@Tii](https://chaos.social/@Tii) - mail@tii.one

Project Link: [https://github.com/TiiFuchs/csv-state-parser](https://github.com/TiiFuchs/csv-state-parser)

<p align="right">(<a href="#readme-top">back to top</a>)</p>




<!-- MARKDOWN LINKS & IMAGES -->

[contributors-shield]: https://img.shields.io/github/contributors/TiiFuchs/csv-state-parser.svg?style=for-the-badge

[contributors-url]: https://github.com/TiiFuchs/csv-state-parser/graphs/contributors

[forks-shield]: https://img.shields.io/github/forks/TiiFuchs/csv-state-parser.svg?style=for-the-badge

[forks-url]: https://github.com/TiiFuchs/csv-state-parser/network/members

[stars-shield]: https://img.shields.io/github/stars/TiiFuchs/csv-state-parser.svg?style=for-the-badge

[stars-url]: https://github.com/TiiFuchs/csv-state-parser/stargazers

[issues-shield]: https://img.shields.io/github/issues/TiiFuchs/csv-state-parser.svg?style=for-the-badge

[issues-url]: https://github.com/TiiFuchs/csv-state-parser/issues

[license-shield]: https://img.shields.io/github/license/TiiFuchs/csv-state-parser.svg?style=for-the-badge

[license-url]: https://github.com/TiiFuchs/csv-state-parser/blob/master/LICENSE.txt