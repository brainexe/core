start
  = expression

expression
  = left:primary operator:operator right:primary { return eval(left + operator+ right); }
  / primary

primary
  = value
  / function
  / method
  / "(" value:expression ")" { return value; }

operator
    = [\=\=\=|\<\=|\>\=|\*\*|\.\.|&&|\=\=|\|\||\!\=|~|%|\-|\*|\/|\<|\||\!|\^|&|\>|\+]

variable
    = [a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]

ws "whitespace" = [ \t\n\r]*

function
  = function:variable
    "("
    arguments:(
      first:value
      rest:(value_separator v:value { return v; })*
      { return [first].concat(rest); }
    )?
    ")"


method
    = variable function

property
    = object "." variable

array_key
    = array .

array
  = begin_array
    values:(
      first:value
      rest:(value_separator v:value { return v; })*
      { return [first].concat(rest); }
    )?
    end_array
    { return values !== null ? values : []; }
  \ variable

object
  = begin_object
    members:(
      first:member
      rest:(value_separator m:member { return m; })*
      {
        var result = {}, i;

        result[first.name] = first.value;

        for (i = 0; i < rest.length; i++) {
          result[rest[i].name] = rest[i].value;
        }

        return result;
      }
    )?
    end_object
    { return members !== null ? members: {}; }

member
  = name:string name_separator value:value {
      return { name: name, value: value };
    }


value
  = false
  / null
  / true
  / object
  / function
  / array
  / number
  / string

number "number"
  = minus? int frac? exp? { return parseFloat(text()); }

string "string"
  = quotation_mark chars:char* quotation_mark { return chars.join(""); }

decimal_point = "."
digit1_9      = [1-9]
e             = [eE]
exp           = e (minus / plus)? DIGIT+
frac          = decimal_point DIGIT+
int           = zero / (digit1_9 DIGIT*)
minus         = "-"
plus          = "+"
zero          = "0"
begin_array     = ws "[" ws
begin_object    = ws "{" ws
end_array       = ws "]" ws
end_object      = ws "}" ws
name_separator  = ws ":" ws
value_separator = ws "," ws

escape         = "\\"
quotation_mark = '"'
unescaped      = [\x20-\x21\x23-\x5B\x5D-\u10FFFF]

char
  = unescaped
  / escape
    sequence:(
        '"'
      / "\\"
      / "/"
      / "b" { return "\b"; }
      / "f" { return "\f"; }
      / "n" { return "\n"; }
      / "r" { return "\r"; }
      / "t" { return "\t"; }
      / "u" digits:$(HEXDIG HEXDIG HEXDIG HEXDIG) {
          return String.fromCharCode(parseInt(digits, 16));
        }
    )
    { return sequence; }

DIGIT  = [0-9]
HEXDIG = [0-9a-f]i

false = "false" { return false; }
null  = "null"  { return null;  }
true  = "true"  { return true;  }

