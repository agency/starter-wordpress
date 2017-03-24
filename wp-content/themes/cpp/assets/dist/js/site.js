"use strict";

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

(function e(t, n, r) {
  function s(o, u) {
    if (!n[o]) {
      if (!t[o]) {
        var a = typeof require == "function" && require;if (!u && a) return a(o, !0);if (i) return i(o, !0);throw new Error("Cannot find module '" + o + "'");
      }var f = n[o] = { exports: {} };t[o][0].call(f.exports, function (e) {
        var n = t[o][1][e];return s(n ? n : e);
      }, f, f.exports, e, t, n, r);
    }return n[o].exports;
  }var i = typeof require == "function" && require;for (var o = 0; o < r.length; o++) {
    s(r[o]);
  }return s;
})({ 1: [function (require, module, exports) {
    (function (process, global, Buffer, __argument0, __argument1, __argument2, __argument3, __filename, __dirname) {
      /*!
       * The buffer module from node.js, for the browser.
       *
       * @author   Feross Aboukhadijeh <feross@feross.org> <http://feross.org>
       * @license  MIT
       */

      var base64 = require('base64-js');
      var ieee754 = require('ieee754');

      exports.Buffer = Buffer;
      exports.SlowBuffer = Buffer;
      exports.INSPECT_MAX_BYTES = 50;
      Buffer.poolSize = 8192;

      /**
       * If `Buffer._useTypedArrays`:
       *   === true    Use Uint8Array implementation (fastest)
       *   === false   Use Object implementation (compatible down to IE6)
       */
      Buffer._useTypedArrays = function () {
        // Detect if browser supports Typed Arrays. Supported browsers are IE 10+, Firefox 4+,
        // Chrome 7+, Safari 5.1+, Opera 11.6+, iOS 4.2+. If the browser does not support adding
        // properties to `Uint8Array` instances, then that's the same as no `Uint8Array` support
        // because we need to be able to add all the node Buffer API methods. This is an issue
        // in Firefox 4-29. Now fixed: https://bugzilla.mozilla.org/show_bug.cgi?id=695438
        try {
          var buf = new ArrayBuffer(0);
          var arr = new Uint8Array(buf);
          arr.foo = function () {
            return 42;
          };
          return 42 === arr.foo() && typeof arr.subarray === 'function'; // Chrome 9-10 lack `subarray`
        } catch (e) {
          return false;
        }
      }();

      /**
       * Class: Buffer
       * =============
       *
       * The Buffer constructor returns instances of `Uint8Array` that are augmented
       * with function properties for all the node `Buffer` API functions. We use
       * `Uint8Array` so that square bracket notation works as expected -- it returns
       * a single octet.
       *
       * By augmenting the instances, we can avoid modifying the `Uint8Array`
       * prototype.
       */
      function Buffer(subject, encoding, noZero) {
        if (!(this instanceof Buffer)) return new Buffer(subject, encoding, noZero);

        var type = typeof subject === "undefined" ? "undefined" : _typeof(subject);

        // Workaround: node's base64 implementation allows for non-padded strings
        // while base64-js does not.
        if (encoding === 'base64' && type === 'string') {
          subject = stringtrim(subject);
          while (subject.length % 4 !== 0) {
            subject = subject + '=';
          }
        }

        // Find the length
        var length;
        if (type === 'number') length = coerce(subject);else if (type === 'string') length = Buffer.byteLength(subject, encoding);else if (type === 'object') length = coerce(subject.length); // assume that object is array-like
        else throw new Error('First argument needs to be a number, array or string.');

        var buf;
        if (Buffer._useTypedArrays) {
          // Preferred: Return an augmented `Uint8Array` instance for best performance
          buf = Buffer._augment(new Uint8Array(length));
        } else {
          // Fallback: Return THIS instance of Buffer (created by `new`)
          buf = this;
          buf.length = length;
          buf._isBuffer = true;
        }

        var i;
        if (Buffer._useTypedArrays && typeof subject.byteLength === 'number') {
          // Speed optimization -- use set if we're copying from a typed array
          buf._set(subject);
        } else if (isArrayish(subject)) {
          // Treat array-ish objects as a byte array
          for (i = 0; i < length; i++) {
            if (Buffer.isBuffer(subject)) buf[i] = subject.readUInt8(i);else buf[i] = subject[i];
          }
        } else if (type === 'string') {
          buf.write(subject, 0, encoding);
        } else if (type === 'number' && !Buffer._useTypedArrays && !noZero) {
          for (i = 0; i < length; i++) {
            buf[i] = 0;
          }
        }

        return buf;
      }

      // STATIC METHODS
      // ==============

      Buffer.isEncoding = function (encoding) {
        switch (String(encoding).toLowerCase()) {
          case 'hex':
          case 'utf8':
          case 'utf-8':
          case 'ascii':
          case 'binary':
          case 'base64':
          case 'raw':
          case 'ucs2':
          case 'ucs-2':
          case 'utf16le':
          case 'utf-16le':
            return true;
          default:
            return false;
        }
      };

      Buffer.isBuffer = function (b) {
        return !!(b !== null && b !== undefined && b._isBuffer);
      };

      Buffer.byteLength = function (str, encoding) {
        var ret;
        str = str + '';
        switch (encoding || 'utf8') {
          case 'hex':
            ret = str.length / 2;
            break;
          case 'utf8':
          case 'utf-8':
            ret = utf8ToBytes(str).length;
            break;
          case 'ascii':
          case 'binary':
          case 'raw':
            ret = str.length;
            break;
          case 'base64':
            ret = base64ToBytes(str).length;
            break;
          case 'ucs2':
          case 'ucs-2':
          case 'utf16le':
          case 'utf-16le':
            ret = str.length * 2;
            break;
          default:
            throw new Error('Unknown encoding');
        }
        return ret;
      };

      Buffer.concat = function (list, totalLength) {
        assert(isArray(list), 'Usage: Buffer.concat(list, [totalLength])\n' + 'list should be an Array.');

        if (list.length === 0) {
          return new Buffer(0);
        } else if (list.length === 1) {
          return list[0];
        }

        var i;
        if (typeof totalLength !== 'number') {
          totalLength = 0;
          for (i = 0; i < list.length; i++) {
            totalLength += list[i].length;
          }
        }

        var buf = new Buffer(totalLength);
        var pos = 0;
        for (i = 0; i < list.length; i++) {
          var item = list[i];
          item.copy(buf, pos);
          pos += item.length;
        }
        return buf;
      };

      // BUFFER INSTANCE METHODS
      // =======================

      function _hexWrite(buf, string, offset, length) {
        offset = Number(offset) || 0;
        var remaining = buf.length - offset;
        if (!length) {
          length = remaining;
        } else {
          length = Number(length);
          if (length > remaining) {
            length = remaining;
          }
        }

        // must be an even number of digits
        var strLen = string.length;
        assert(strLen % 2 === 0, 'Invalid hex string');

        if (length > strLen / 2) {
          length = strLen / 2;
        }
        for (var i = 0; i < length; i++) {
          var byte = parseInt(string.substr(i * 2, 2), 16);
          assert(!isNaN(byte), 'Invalid hex string');
          buf[offset + i] = byte;
        }
        Buffer._charsWritten = i * 2;
        return i;
      }

      function _utf8Write(buf, string, offset, length) {
        var charsWritten = Buffer._charsWritten = blitBuffer(utf8ToBytes(string), buf, offset, length);
        return charsWritten;
      }

      function _asciiWrite(buf, string, offset, length) {
        var charsWritten = Buffer._charsWritten = blitBuffer(asciiToBytes(string), buf, offset, length);
        return charsWritten;
      }

      function _binaryWrite(buf, string, offset, length) {
        return _asciiWrite(buf, string, offset, length);
      }

      function _base64Write(buf, string, offset, length) {
        var charsWritten = Buffer._charsWritten = blitBuffer(base64ToBytes(string), buf, offset, length);
        return charsWritten;
      }

      function _utf16leWrite(buf, string, offset, length) {
        var charsWritten = Buffer._charsWritten = blitBuffer(utf16leToBytes(string), buf, offset, length);
        return charsWritten;
      }

      Buffer.prototype.write = function (string, offset, length, encoding) {
        // Support both (string, offset, length, encoding)
        // and the legacy (string, encoding, offset, length)
        if (isFinite(offset)) {
          if (!isFinite(length)) {
            encoding = length;
            length = undefined;
          }
        } else {
          // legacy
          var swap = encoding;
          encoding = offset;
          offset = length;
          length = swap;
        }

        offset = Number(offset) || 0;
        var remaining = this.length - offset;
        if (!length) {
          length = remaining;
        } else {
          length = Number(length);
          if (length > remaining) {
            length = remaining;
          }
        }
        encoding = String(encoding || 'utf8').toLowerCase();

        var ret;
        switch (encoding) {
          case 'hex':
            ret = _hexWrite(this, string, offset, length);
            break;
          case 'utf8':
          case 'utf-8':
            ret = _utf8Write(this, string, offset, length);
            break;
          case 'ascii':
            ret = _asciiWrite(this, string, offset, length);
            break;
          case 'binary':
            ret = _binaryWrite(this, string, offset, length);
            break;
          case 'base64':
            ret = _base64Write(this, string, offset, length);
            break;
          case 'ucs2':
          case 'ucs-2':
          case 'utf16le':
          case 'utf-16le':
            ret = _utf16leWrite(this, string, offset, length);
            break;
          default:
            throw new Error('Unknown encoding');
        }
        return ret;
      };

      Buffer.prototype.toString = function (encoding, start, end) {
        var self = this;

        encoding = String(encoding || 'utf8').toLowerCase();
        start = Number(start) || 0;
        end = end !== undefined ? Number(end) : end = self.length;

        // Fastpath empty strings
        if (end === start) return '';

        var ret;
        switch (encoding) {
          case 'hex':
            ret = _hexSlice(self, start, end);
            break;
          case 'utf8':
          case 'utf-8':
            ret = _utf8Slice(self, start, end);
            break;
          case 'ascii':
            ret = _asciiSlice(self, start, end);
            break;
          case 'binary':
            ret = _binarySlice(self, start, end);
            break;
          case 'base64':
            ret = _base64Slice(self, start, end);
            break;
          case 'ucs2':
          case 'ucs-2':
          case 'utf16le':
          case 'utf-16le':
            ret = _utf16leSlice(self, start, end);
            break;
          default:
            throw new Error('Unknown encoding');
        }
        return ret;
      };

      Buffer.prototype.toJSON = function () {
        return {
          type: 'Buffer',
          data: Array.prototype.slice.call(this._arr || this, 0)
        };
      };

      // copy(targetBuffer, targetStart=0, sourceStart=0, sourceEnd=buffer.length)
      Buffer.prototype.copy = function (target, target_start, start, end) {
        var source = this;

        if (!start) start = 0;
        if (!end && end !== 0) end = this.length;
        if (!target_start) target_start = 0;

        // Copy 0 bytes; we're done
        if (end === start) return;
        if (target.length === 0 || source.length === 0) return;

        // Fatal error conditions
        assert(end >= start, 'sourceEnd < sourceStart');
        assert(target_start >= 0 && target_start < target.length, 'targetStart out of bounds');
        assert(start >= 0 && start < source.length, 'sourceStart out of bounds');
        assert(end >= 0 && end <= source.length, 'sourceEnd out of bounds');

        // Are we oob?
        if (end > this.length) end = this.length;
        if (target.length - target_start < end - start) end = target.length - target_start + start;

        var len = end - start;

        if (len < 100 || !Buffer._useTypedArrays) {
          for (var i = 0; i < len; i++) {
            target[i + target_start] = this[i + start];
          }
        } else {
          target._set(this.subarray(start, start + len), target_start);
        }
      };

      function _base64Slice(buf, start, end) {
        if (start === 0 && end === buf.length) {
          return base64.fromByteArray(buf);
        } else {
          return base64.fromByteArray(buf.slice(start, end));
        }
      }

      function _utf8Slice(buf, start, end) {
        var res = '';
        var tmp = '';
        end = Math.min(buf.length, end);

        for (var i = start; i < end; i++) {
          if (buf[i] <= 0x7F) {
            res += decodeUtf8Char(tmp) + String.fromCharCode(buf[i]);
            tmp = '';
          } else {
            tmp += '%' + buf[i].toString(16);
          }
        }

        return res + decodeUtf8Char(tmp);
      }

      function _asciiSlice(buf, start, end) {
        var ret = '';
        end = Math.min(buf.length, end);

        for (var i = start; i < end; i++) {
          ret += String.fromCharCode(buf[i]);
        }return ret;
      }

      function _binarySlice(buf, start, end) {
        return _asciiSlice(buf, start, end);
      }

      function _hexSlice(buf, start, end) {
        var len = buf.length;

        if (!start || start < 0) start = 0;
        if (!end || end < 0 || end > len) end = len;

        var out = '';
        for (var i = start; i < end; i++) {
          out += toHex(buf[i]);
        }
        return out;
      }

      function _utf16leSlice(buf, start, end) {
        var bytes = buf.slice(start, end);
        var res = '';
        for (var i = 0; i < bytes.length; i += 2) {
          res += String.fromCharCode(bytes[i] + bytes[i + 1] * 256);
        }
        return res;
      }

      Buffer.prototype.slice = function (start, end) {
        var len = this.length;
        start = clamp(start, len, 0);
        end = clamp(end, len, len);

        if (Buffer._useTypedArrays) {
          return Buffer._augment(this.subarray(start, end));
        } else {
          var sliceLen = end - start;
          var newBuf = new Buffer(sliceLen, undefined, true);
          for (var i = 0; i < sliceLen; i++) {
            newBuf[i] = this[i + start];
          }
          return newBuf;
        }
      };

      // `get` will be removed in Node 0.13+
      Buffer.prototype.get = function (offset) {
        console.log('.get() is deprecated. Access using array indexes instead.');
        return this.readUInt8(offset);
      };

      // `set` will be removed in Node 0.13+
      Buffer.prototype.set = function (v, offset) {
        console.log('.set() is deprecated. Access using array indexes instead.');
        return this.writeUInt8(v, offset);
      };

      Buffer.prototype.readUInt8 = function (offset, noAssert) {
        if (!noAssert) {
          assert(offset !== undefined && offset !== null, 'missing offset');
          assert(offset < this.length, 'Trying to read beyond buffer length');
        }

        if (offset >= this.length) return;

        return this[offset];
      };

      function _readUInt16(buf, offset, littleEndian, noAssert) {
        if (!noAssert) {
          assert(typeof littleEndian === 'boolean', 'missing or invalid endian');
          assert(offset !== undefined && offset !== null, 'missing offset');
          assert(offset + 1 < buf.length, 'Trying to read beyond buffer length');
        }

        var len = buf.length;
        if (offset >= len) return;

        var val;
        if (littleEndian) {
          val = buf[offset];
          if (offset + 1 < len) val |= buf[offset + 1] << 8;
        } else {
          val = buf[offset] << 8;
          if (offset + 1 < len) val |= buf[offset + 1];
        }
        return val;
      }

      Buffer.prototype.readUInt16LE = function (offset, noAssert) {
        return _readUInt16(this, offset, true, noAssert);
      };

      Buffer.prototype.readUInt16BE = function (offset, noAssert) {
        return _readUInt16(this, offset, false, noAssert);
      };

      function _readUInt32(buf, offset, littleEndian, noAssert) {
        if (!noAssert) {
          assert(typeof littleEndian === 'boolean', 'missing or invalid endian');
          assert(offset !== undefined && offset !== null, 'missing offset');
          assert(offset + 3 < buf.length, 'Trying to read beyond buffer length');
        }

        var len = buf.length;
        if (offset >= len) return;

        var val;
        if (littleEndian) {
          if (offset + 2 < len) val = buf[offset + 2] << 16;
          if (offset + 1 < len) val |= buf[offset + 1] << 8;
          val |= buf[offset];
          if (offset + 3 < len) val = val + (buf[offset + 3] << 24 >>> 0);
        } else {
          if (offset + 1 < len) val = buf[offset + 1] << 16;
          if (offset + 2 < len) val |= buf[offset + 2] << 8;
          if (offset + 3 < len) val |= buf[offset + 3];
          val = val + (buf[offset] << 24 >>> 0);
        }
        return val;
      }

      Buffer.prototype.readUInt32LE = function (offset, noAssert) {
        return _readUInt32(this, offset, true, noAssert);
      };

      Buffer.prototype.readUInt32BE = function (offset, noAssert) {
        return _readUInt32(this, offset, false, noAssert);
      };

      Buffer.prototype.readInt8 = function (offset, noAssert) {
        if (!noAssert) {
          assert(offset !== undefined && offset !== null, 'missing offset');
          assert(offset < this.length, 'Trying to read beyond buffer length');
        }

        if (offset >= this.length) return;

        var neg = this[offset] & 0x80;
        if (neg) return (0xff - this[offset] + 1) * -1;else return this[offset];
      };

      function _readInt16(buf, offset, littleEndian, noAssert) {
        if (!noAssert) {
          assert(typeof littleEndian === 'boolean', 'missing or invalid endian');
          assert(offset !== undefined && offset !== null, 'missing offset');
          assert(offset + 1 < buf.length, 'Trying to read beyond buffer length');
        }

        var len = buf.length;
        if (offset >= len) return;

        var val = _readUInt16(buf, offset, littleEndian, true);
        var neg = val & 0x8000;
        if (neg) return (0xffff - val + 1) * -1;else return val;
      }

      Buffer.prototype.readInt16LE = function (offset, noAssert) {
        return _readInt16(this, offset, true, noAssert);
      };

      Buffer.prototype.readInt16BE = function (offset, noAssert) {
        return _readInt16(this, offset, false, noAssert);
      };

      function _readInt32(buf, offset, littleEndian, noAssert) {
        if (!noAssert) {
          assert(typeof littleEndian === 'boolean', 'missing or invalid endian');
          assert(offset !== undefined && offset !== null, 'missing offset');
          assert(offset + 3 < buf.length, 'Trying to read beyond buffer length');
        }

        var len = buf.length;
        if (offset >= len) return;

        var val = _readUInt32(buf, offset, littleEndian, true);
        var neg = val & 0x80000000;
        if (neg) return (0xffffffff - val + 1) * -1;else return val;
      }

      Buffer.prototype.readInt32LE = function (offset, noAssert) {
        return _readInt32(this, offset, true, noAssert);
      };

      Buffer.prototype.readInt32BE = function (offset, noAssert) {
        return _readInt32(this, offset, false, noAssert);
      };

      function _readFloat(buf, offset, littleEndian, noAssert) {
        if (!noAssert) {
          assert(typeof littleEndian === 'boolean', 'missing or invalid endian');
          assert(offset + 3 < buf.length, 'Trying to read beyond buffer length');
        }

        return ieee754.read(buf, offset, littleEndian, 23, 4);
      }

      Buffer.prototype.readFloatLE = function (offset, noAssert) {
        return _readFloat(this, offset, true, noAssert);
      };

      Buffer.prototype.readFloatBE = function (offset, noAssert) {
        return _readFloat(this, offset, false, noAssert);
      };

      function _readDouble(buf, offset, littleEndian, noAssert) {
        if (!noAssert) {
          assert(typeof littleEndian === 'boolean', 'missing or invalid endian');
          assert(offset + 7 < buf.length, 'Trying to read beyond buffer length');
        }

        return ieee754.read(buf, offset, littleEndian, 52, 8);
      }

      Buffer.prototype.readDoubleLE = function (offset, noAssert) {
        return _readDouble(this, offset, true, noAssert);
      };

      Buffer.prototype.readDoubleBE = function (offset, noAssert) {
        return _readDouble(this, offset, false, noAssert);
      };

      Buffer.prototype.writeUInt8 = function (value, offset, noAssert) {
        if (!noAssert) {
          assert(value !== undefined && value !== null, 'missing value');
          assert(offset !== undefined && offset !== null, 'missing offset');
          assert(offset < this.length, 'trying to write beyond buffer length');
          verifuint(value, 0xff);
        }

        if (offset >= this.length) return;

        this[offset] = value;
      };

      function _writeUInt16(buf, value, offset, littleEndian, noAssert) {
        if (!noAssert) {
          assert(value !== undefined && value !== null, 'missing value');
          assert(typeof littleEndian === 'boolean', 'missing or invalid endian');
          assert(offset !== undefined && offset !== null, 'missing offset');
          assert(offset + 1 < buf.length, 'trying to write beyond buffer length');
          verifuint(value, 0xffff);
        }

        var len = buf.length;
        if (offset >= len) return;

        for (var i = 0, j = Math.min(len - offset, 2); i < j; i++) {
          buf[offset + i] = (value & 0xff << 8 * (littleEndian ? i : 1 - i)) >>> (littleEndian ? i : 1 - i) * 8;
        }
      }

      Buffer.prototype.writeUInt16LE = function (value, offset, noAssert) {
        _writeUInt16(this, value, offset, true, noAssert);
      };

      Buffer.prototype.writeUInt16BE = function (value, offset, noAssert) {
        _writeUInt16(this, value, offset, false, noAssert);
      };

      function _writeUInt32(buf, value, offset, littleEndian, noAssert) {
        if (!noAssert) {
          assert(value !== undefined && value !== null, 'missing value');
          assert(typeof littleEndian === 'boolean', 'missing or invalid endian');
          assert(offset !== undefined && offset !== null, 'missing offset');
          assert(offset + 3 < buf.length, 'trying to write beyond buffer length');
          verifuint(value, 0xffffffff);
        }

        var len = buf.length;
        if (offset >= len) return;

        for (var i = 0, j = Math.min(len - offset, 4); i < j; i++) {
          buf[offset + i] = value >>> (littleEndian ? i : 3 - i) * 8 & 0xff;
        }
      }

      Buffer.prototype.writeUInt32LE = function (value, offset, noAssert) {
        _writeUInt32(this, value, offset, true, noAssert);
      };

      Buffer.prototype.writeUInt32BE = function (value, offset, noAssert) {
        _writeUInt32(this, value, offset, false, noAssert);
      };

      Buffer.prototype.writeInt8 = function (value, offset, noAssert) {
        if (!noAssert) {
          assert(value !== undefined && value !== null, 'missing value');
          assert(offset !== undefined && offset !== null, 'missing offset');
          assert(offset < this.length, 'Trying to write beyond buffer length');
          verifsint(value, 0x7f, -0x80);
        }

        if (offset >= this.length) return;

        if (value >= 0) this.writeUInt8(value, offset, noAssert);else this.writeUInt8(0xff + value + 1, offset, noAssert);
      };

      function _writeInt16(buf, value, offset, littleEndian, noAssert) {
        if (!noAssert) {
          assert(value !== undefined && value !== null, 'missing value');
          assert(typeof littleEndian === 'boolean', 'missing or invalid endian');
          assert(offset !== undefined && offset !== null, 'missing offset');
          assert(offset + 1 < buf.length, 'Trying to write beyond buffer length');
          verifsint(value, 0x7fff, -0x8000);
        }

        var len = buf.length;
        if (offset >= len) return;

        if (value >= 0) _writeUInt16(buf, value, offset, littleEndian, noAssert);else _writeUInt16(buf, 0xffff + value + 1, offset, littleEndian, noAssert);
      }

      Buffer.prototype.writeInt16LE = function (value, offset, noAssert) {
        _writeInt16(this, value, offset, true, noAssert);
      };

      Buffer.prototype.writeInt16BE = function (value, offset, noAssert) {
        _writeInt16(this, value, offset, false, noAssert);
      };

      function _writeInt32(buf, value, offset, littleEndian, noAssert) {
        if (!noAssert) {
          assert(value !== undefined && value !== null, 'missing value');
          assert(typeof littleEndian === 'boolean', 'missing or invalid endian');
          assert(offset !== undefined && offset !== null, 'missing offset');
          assert(offset + 3 < buf.length, 'Trying to write beyond buffer length');
          verifsint(value, 0x7fffffff, -0x80000000);
        }

        var len = buf.length;
        if (offset >= len) return;

        if (value >= 0) _writeUInt32(buf, value, offset, littleEndian, noAssert);else _writeUInt32(buf, 0xffffffff + value + 1, offset, littleEndian, noAssert);
      }

      Buffer.prototype.writeInt32LE = function (value, offset, noAssert) {
        _writeInt32(this, value, offset, true, noAssert);
      };

      Buffer.prototype.writeInt32BE = function (value, offset, noAssert) {
        _writeInt32(this, value, offset, false, noAssert);
      };

      function _writeFloat(buf, value, offset, littleEndian, noAssert) {
        if (!noAssert) {
          assert(value !== undefined && value !== null, 'missing value');
          assert(typeof littleEndian === 'boolean', 'missing or invalid endian');
          assert(offset !== undefined && offset !== null, 'missing offset');
          assert(offset + 3 < buf.length, 'Trying to write beyond buffer length');
          verifIEEE754(value, 3.4028234663852886e+38, -3.4028234663852886e+38);
        }

        var len = buf.length;
        if (offset >= len) return;

        ieee754.write(buf, value, offset, littleEndian, 23, 4);
      }

      Buffer.prototype.writeFloatLE = function (value, offset, noAssert) {
        _writeFloat(this, value, offset, true, noAssert);
      };

      Buffer.prototype.writeFloatBE = function (value, offset, noAssert) {
        _writeFloat(this, value, offset, false, noAssert);
      };

      function _writeDouble(buf, value, offset, littleEndian, noAssert) {
        if (!noAssert) {
          assert(value !== undefined && value !== null, 'missing value');
          assert(typeof littleEndian === 'boolean', 'missing or invalid endian');
          assert(offset !== undefined && offset !== null, 'missing offset');
          assert(offset + 7 < buf.length, 'Trying to write beyond buffer length');
          verifIEEE754(value, 1.7976931348623157E+308, -1.7976931348623157E+308);
        }

        var len = buf.length;
        if (offset >= len) return;

        ieee754.write(buf, value, offset, littleEndian, 52, 8);
      }

      Buffer.prototype.writeDoubleLE = function (value, offset, noAssert) {
        _writeDouble(this, value, offset, true, noAssert);
      };

      Buffer.prototype.writeDoubleBE = function (value, offset, noAssert) {
        _writeDouble(this, value, offset, false, noAssert);
      };

      // fill(value, start=0, end=buffer.length)
      Buffer.prototype.fill = function (value, start, end) {
        if (!value) value = 0;
        if (!start) start = 0;
        if (!end) end = this.length;

        if (typeof value === 'string') {
          value = value.charCodeAt(0);
        }

        assert(typeof value === 'number' && !isNaN(value), 'value is not a number');
        assert(end >= start, 'end < start');

        // Fill 0 bytes; we're done
        if (end === start) return;
        if (this.length === 0) return;

        assert(start >= 0 && start < this.length, 'start out of bounds');
        assert(end >= 0 && end <= this.length, 'end out of bounds');

        for (var i = start; i < end; i++) {
          this[i] = value;
        }
      };

      Buffer.prototype.inspect = function () {
        var out = [];
        var len = this.length;
        for (var i = 0; i < len; i++) {
          out[i] = toHex(this[i]);
          if (i === exports.INSPECT_MAX_BYTES) {
            out[i + 1] = '...';
            break;
          }
        }
        return '<Buffer ' + out.join(' ') + '>';
      };

      /**
       * Creates a new `ArrayBuffer` with the *copied* memory of the buffer instance.
       * Added in Node 0.12. Only available in browsers that support ArrayBuffer.
       */
      Buffer.prototype.toArrayBuffer = function () {
        if (typeof Uint8Array !== 'undefined') {
          if (Buffer._useTypedArrays) {
            return new Buffer(this).buffer;
          } else {
            var buf = new Uint8Array(this.length);
            for (var i = 0, len = buf.length; i < len; i += 1) {
              buf[i] = this[i];
            }return buf.buffer;
          }
        } else {
          throw new Error('Buffer.toArrayBuffer not supported in this browser');
        }
      };

      // HELPER FUNCTIONS
      // ================

      function stringtrim(str) {
        if (str.trim) return str.trim();
        return str.replace(/^\s+|\s+$/g, '');
      }

      var BP = Buffer.prototype;

      /**
       * Augment a Uint8Array *instance* (not the Uint8Array class!) with Buffer methods
       */
      Buffer._augment = function (arr) {
        arr._isBuffer = true;

        // save reference to original Uint8Array get/set methods before overwriting
        arr._get = arr.get;
        arr._set = arr.set;

        // deprecated, will be removed in node 0.13+
        arr.get = BP.get;
        arr.set = BP.set;

        arr.write = BP.write;
        arr.toString = BP.toString;
        arr.toLocaleString = BP.toString;
        arr.toJSON = BP.toJSON;
        arr.copy = BP.copy;
        arr.slice = BP.slice;
        arr.readUInt8 = BP.readUInt8;
        arr.readUInt16LE = BP.readUInt16LE;
        arr.readUInt16BE = BP.readUInt16BE;
        arr.readUInt32LE = BP.readUInt32LE;
        arr.readUInt32BE = BP.readUInt32BE;
        arr.readInt8 = BP.readInt8;
        arr.readInt16LE = BP.readInt16LE;
        arr.readInt16BE = BP.readInt16BE;
        arr.readInt32LE = BP.readInt32LE;
        arr.readInt32BE = BP.readInt32BE;
        arr.readFloatLE = BP.readFloatLE;
        arr.readFloatBE = BP.readFloatBE;
        arr.readDoubleLE = BP.readDoubleLE;
        arr.readDoubleBE = BP.readDoubleBE;
        arr.writeUInt8 = BP.writeUInt8;
        arr.writeUInt16LE = BP.writeUInt16LE;
        arr.writeUInt16BE = BP.writeUInt16BE;
        arr.writeUInt32LE = BP.writeUInt32LE;
        arr.writeUInt32BE = BP.writeUInt32BE;
        arr.writeInt8 = BP.writeInt8;
        arr.writeInt16LE = BP.writeInt16LE;
        arr.writeInt16BE = BP.writeInt16BE;
        arr.writeInt32LE = BP.writeInt32LE;
        arr.writeInt32BE = BP.writeInt32BE;
        arr.writeFloatLE = BP.writeFloatLE;
        arr.writeFloatBE = BP.writeFloatBE;
        arr.writeDoubleLE = BP.writeDoubleLE;
        arr.writeDoubleBE = BP.writeDoubleBE;
        arr.fill = BP.fill;
        arr.inspect = BP.inspect;
        arr.toArrayBuffer = BP.toArrayBuffer;

        return arr;
      };

      // slice(start, end)
      function clamp(index, len, defaultValue) {
        if (typeof index !== 'number') return defaultValue;
        index = ~~index; // Coerce to integer.
        if (index >= len) return len;
        if (index >= 0) return index;
        index += len;
        if (index >= 0) return index;
        return 0;
      }

      function coerce(length) {
        // Coerce length to a number (possibly NaN), round up
        // in case it's fractional (e.g. 123.456) then do a
        // double negate to coerce a NaN to 0. Easy, right?
        length = ~~Math.ceil(+length);
        return length < 0 ? 0 : length;
      }

      function isArray(subject) {
        return (Array.isArray || function (subject) {
          return Object.prototype.toString.call(subject) === '[object Array]';
        })(subject);
      }

      function isArrayish(subject) {
        return isArray(subject) || Buffer.isBuffer(subject) || subject && (typeof subject === "undefined" ? "undefined" : _typeof(subject)) === 'object' && typeof subject.length === 'number';
      }

      function toHex(n) {
        if (n < 16) return '0' + n.toString(16);
        return n.toString(16);
      }

      function utf8ToBytes(str) {
        var byteArray = [];
        for (var i = 0; i < str.length; i++) {
          var b = str.charCodeAt(i);
          if (b <= 0x7F) byteArray.push(str.charCodeAt(i));else {
            var start = i;
            if (b >= 0xD800 && b <= 0xDFFF) i++;
            var h = encodeURIComponent(str.slice(start, i + 1)).substr(1).split('%');
            for (var j = 0; j < h.length; j++) {
              byteArray.push(parseInt(h[j], 16));
            }
          }
        }
        return byteArray;
      }

      function asciiToBytes(str) {
        var byteArray = [];
        for (var i = 0; i < str.length; i++) {
          // Node's code seems to be doing this and not & 0x7F..
          byteArray.push(str.charCodeAt(i) & 0xFF);
        }
        return byteArray;
      }

      function utf16leToBytes(str) {
        var c, hi, lo;
        var byteArray = [];
        for (var i = 0; i < str.length; i++) {
          c = str.charCodeAt(i);
          hi = c >> 8;
          lo = c % 256;
          byteArray.push(lo);
          byteArray.push(hi);
        }

        return byteArray;
      }

      function base64ToBytes(str) {
        return base64.toByteArray(str);
      }

      function blitBuffer(src, dst, offset, length) {
        var pos;
        for (var i = 0; i < length; i++) {
          if (i + offset >= dst.length || i >= src.length) break;
          dst[i + offset] = src[i];
        }
        return i;
      }

      function decodeUtf8Char(str) {
        try {
          return decodeURIComponent(str);
        } catch (err) {
          return String.fromCharCode(0xFFFD); // UTF 8 invalid char
        }
      }

      /*
       * We have to make sure that the value is a valid integer. This means that it
       * is non-negative. It has no fractional component and that it does not
       * exceed the maximum allowed value.
       */
      function verifuint(value, max) {
        assert(typeof value === 'number', 'cannot write a non-number as a number');
        assert(value >= 0, 'specified a negative value for writing an unsigned value');
        assert(value <= max, 'value is larger than maximum value for type');
        assert(Math.floor(value) === value, 'value has a fractional component');
      }

      function verifsint(value, max, min) {
        assert(typeof value === 'number', 'cannot write a non-number as a number');
        assert(value <= max, 'value larger than maximum allowed value');
        assert(value >= min, 'value smaller than minimum allowed value');
        assert(Math.floor(value) === value, 'value has a fractional component');
      }

      function verifIEEE754(value, max, min) {
        assert(typeof value === 'number', 'cannot write a non-number as a number');
        assert(value <= max, 'value larger than maximum allowed value');
        assert(value >= min, 'value smaller than minimum allowed value');
      }

      function assert(test, message) {
        if (!test) throw new Error(message || 'Failed assertion');
      }
    }).call(this, require("oMfpAn"), typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {}, require("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/../../node_modules/gulp-browserify/node_modules/browserify/node_modules/buffer/index.js", "/../../node_modules/gulp-browserify/node_modules/browserify/node_modules/buffer");
  }, { "base64-js": 2, "buffer": 1, "ieee754": 3, "oMfpAn": 4 }], 2: [function (require, module, exports) {
    (function (process, global, Buffer, __argument0, __argument1, __argument2, __argument3, __filename, __dirname) {
      var lookup = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

      ;(function (exports) {
        'use strict';

        var Arr = typeof Uint8Array !== 'undefined' ? Uint8Array : Array;

        var PLUS = '+'.charCodeAt(0);
        var SLASH = '/'.charCodeAt(0);
        var NUMBER = '0'.charCodeAt(0);
        var LOWER = 'a'.charCodeAt(0);
        var UPPER = 'A'.charCodeAt(0);
        var PLUS_URL_SAFE = '-'.charCodeAt(0);
        var SLASH_URL_SAFE = '_'.charCodeAt(0);

        function decode(elt) {
          var code = elt.charCodeAt(0);
          if (code === PLUS || code === PLUS_URL_SAFE) return 62; // '+'
          if (code === SLASH || code === SLASH_URL_SAFE) return 63; // '/'
          if (code < NUMBER) return -1; //no match
          if (code < NUMBER + 10) return code - NUMBER + 26 + 26;
          if (code < UPPER + 26) return code - UPPER;
          if (code < LOWER + 26) return code - LOWER + 26;
        }

        function b64ToByteArray(b64) {
          var i, j, l, tmp, placeHolders, arr;

          if (b64.length % 4 > 0) {
            throw new Error('Invalid string. Length must be a multiple of 4');
          }

          // the number of equal signs (place holders)
          // if there are two placeholders, than the two characters before it
          // represent one byte
          // if there is only one, then the three characters before it represent 2 bytes
          // this is just a cheap hack to not do indexOf twice
          var len = b64.length;
          placeHolders = '=' === b64.charAt(len - 2) ? 2 : '=' === b64.charAt(len - 1) ? 1 : 0;

          // base64 is 4/3 + up to two characters of the original data
          arr = new Arr(b64.length * 3 / 4 - placeHolders);

          // if there are placeholders, only get up to the last complete 4 chars
          l = placeHolders > 0 ? b64.length - 4 : b64.length;

          var L = 0;

          function push(v) {
            arr[L++] = v;
          }

          for (i = 0, j = 0; i < l; i += 4, j += 3) {
            tmp = decode(b64.charAt(i)) << 18 | decode(b64.charAt(i + 1)) << 12 | decode(b64.charAt(i + 2)) << 6 | decode(b64.charAt(i + 3));
            push((tmp & 0xFF0000) >> 16);
            push((tmp & 0xFF00) >> 8);
            push(tmp & 0xFF);
          }

          if (placeHolders === 2) {
            tmp = decode(b64.charAt(i)) << 2 | decode(b64.charAt(i + 1)) >> 4;
            push(tmp & 0xFF);
          } else if (placeHolders === 1) {
            tmp = decode(b64.charAt(i)) << 10 | decode(b64.charAt(i + 1)) << 4 | decode(b64.charAt(i + 2)) >> 2;
            push(tmp >> 8 & 0xFF);
            push(tmp & 0xFF);
          }

          return arr;
        }

        function uint8ToBase64(uint8) {
          var i,
              extraBytes = uint8.length % 3,
              // if we have 1 byte left, pad 2 bytes
          output = "",
              temp,
              length;

          function encode(num) {
            return lookup.charAt(num);
          }

          function tripletToBase64(num) {
            return encode(num >> 18 & 0x3F) + encode(num >> 12 & 0x3F) + encode(num >> 6 & 0x3F) + encode(num & 0x3F);
          }

          // go through the array every three bytes, we'll deal with trailing stuff later
          for (i = 0, length = uint8.length - extraBytes; i < length; i += 3) {
            temp = (uint8[i] << 16) + (uint8[i + 1] << 8) + uint8[i + 2];
            output += tripletToBase64(temp);
          }

          // pad the end with zeros, but make sure to not forget the extra bytes
          switch (extraBytes) {
            case 1:
              temp = uint8[uint8.length - 1];
              output += encode(temp >> 2);
              output += encode(temp << 4 & 0x3F);
              output += '==';
              break;
            case 2:
              temp = (uint8[uint8.length - 2] << 8) + uint8[uint8.length - 1];
              output += encode(temp >> 10);
              output += encode(temp >> 4 & 0x3F);
              output += encode(temp << 2 & 0x3F);
              output += '=';
              break;
          }

          return output;
        }

        exports.toByteArray = b64ToByteArray;
        exports.fromByteArray = uint8ToBase64;
      })(typeof exports === 'undefined' ? this.base64js = {} : exports);
    }).call(this, require("oMfpAn"), typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {}, require("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/../../node_modules/gulp-browserify/node_modules/browserify/node_modules/buffer/node_modules/base64-js/lib/b64.js", "/../../node_modules/gulp-browserify/node_modules/browserify/node_modules/buffer/node_modules/base64-js/lib");
  }, { "buffer": 1, "oMfpAn": 4 }], 3: [function (require, module, exports) {
    (function (process, global, Buffer, __argument0, __argument1, __argument2, __argument3, __filename, __dirname) {
      exports.read = function (buffer, offset, isLE, mLen, nBytes) {
        var e, m;
        var eLen = nBytes * 8 - mLen - 1;
        var eMax = (1 << eLen) - 1;
        var eBias = eMax >> 1;
        var nBits = -7;
        var i = isLE ? nBytes - 1 : 0;
        var d = isLE ? -1 : 1;
        var s = buffer[offset + i];

        i += d;

        e = s & (1 << -nBits) - 1;
        s >>= -nBits;
        nBits += eLen;
        for (; nBits > 0; e = e * 256 + buffer[offset + i], i += d, nBits -= 8) {}

        m = e & (1 << -nBits) - 1;
        e >>= -nBits;
        nBits += mLen;
        for (; nBits > 0; m = m * 256 + buffer[offset + i], i += d, nBits -= 8) {}

        if (e === 0) {
          e = 1 - eBias;
        } else if (e === eMax) {
          return m ? NaN : (s ? -1 : 1) * Infinity;
        } else {
          m = m + Math.pow(2, mLen);
          e = e - eBias;
        }
        return (s ? -1 : 1) * m * Math.pow(2, e - mLen);
      };

      exports.write = function (buffer, value, offset, isLE, mLen, nBytes) {
        var e, m, c;
        var eLen = nBytes * 8 - mLen - 1;
        var eMax = (1 << eLen) - 1;
        var eBias = eMax >> 1;
        var rt = mLen === 23 ? Math.pow(2, -24) - Math.pow(2, -77) : 0;
        var i = isLE ? 0 : nBytes - 1;
        var d = isLE ? 1 : -1;
        var s = value < 0 || value === 0 && 1 / value < 0 ? 1 : 0;

        value = Math.abs(value);

        if (isNaN(value) || value === Infinity) {
          m = isNaN(value) ? 1 : 0;
          e = eMax;
        } else {
          e = Math.floor(Math.log(value) / Math.LN2);
          if (value * (c = Math.pow(2, -e)) < 1) {
            e--;
            c *= 2;
          }
          if (e + eBias >= 1) {
            value += rt / c;
          } else {
            value += rt * Math.pow(2, 1 - eBias);
          }
          if (value * c >= 2) {
            e++;
            c /= 2;
          }

          if (e + eBias >= eMax) {
            m = 0;
            e = eMax;
          } else if (e + eBias >= 1) {
            m = (value * c - 1) * Math.pow(2, mLen);
            e = e + eBias;
          } else {
            m = value * Math.pow(2, eBias - 1) * Math.pow(2, mLen);
            e = 0;
          }
        }

        for (; mLen >= 8; buffer[offset + i] = m & 0xff, i += d, m /= 256, mLen -= 8) {}

        e = e << mLen | m;
        eLen += mLen;
        for (; eLen > 0; buffer[offset + i] = e & 0xff, i += d, e /= 256, eLen -= 8) {}

        buffer[offset + i - d] |= s * 128;
      };
    }).call(this, require("oMfpAn"), typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {}, require("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/../../node_modules/gulp-browserify/node_modules/browserify/node_modules/buffer/node_modules/ieee754/index.js", "/../../node_modules/gulp-browserify/node_modules/browserify/node_modules/buffer/node_modules/ieee754");
  }, { "buffer": 1, "oMfpAn": 4 }], 4: [function (require, module, exports) {
    (function (process, global, Buffer, __argument0, __argument1, __argument2, __argument3, __filename, __dirname) {
      // shim for using process in browser

      var process = module.exports = {};

      process.nextTick = function () {
        var canSetImmediate = typeof window !== 'undefined' && window.setImmediate;
        var canPost = typeof window !== 'undefined' && window.postMessage && window.addEventListener;

        if (canSetImmediate) {
          return function (f) {
            return window.setImmediate(f);
          };
        }

        if (canPost) {
          var queue = [];
          window.addEventListener('message', function (ev) {
            var source = ev.source;
            if ((source === window || source === null) && ev.data === 'process-tick') {
              ev.stopPropagation();
              if (queue.length > 0) {
                var fn = queue.shift();
                fn();
              }
            }
          }, true);

          return function nextTick(fn) {
            queue.push(fn);
            window.postMessage('process-tick', '*');
          };
        }

        return function nextTick(fn) {
          setTimeout(fn, 0);
        };
      }();

      process.title = 'browser';
      process.browser = true;
      process.env = {};
      process.argv = [];

      function noop() {}

      process.on = noop;
      process.addListener = noop;
      process.once = noop;
      process.off = noop;
      process.removeListener = noop;
      process.removeAllListeners = noop;
      process.emit = noop;

      process.binding = function (name) {
        throw new Error('process.binding is not supported');
      };

      // TODO(shtylman)
      process.cwd = function () {
        return '/';
      };
      process.chdir = function (dir) {
        throw new Error('process.chdir is not supported');
      };
    }).call(this, require("oMfpAn"), typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {}, require("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/../../node_modules/gulp-browserify/node_modules/browserify/node_modules/process/browser.js", "/../../node_modules/gulp-browserify/node_modules/browserify/node_modules/process");
  }, { "buffer": 1, "oMfpAn": 4 }], 5: [function (require, module, exports) {
    (function (process, global, Buffer, __argument0, __argument1, __argument2, __argument3, __filename, __dirname) {
      /*	
       * Theme Javascript Initializer
       */

      require('./plugins/share');
      // require('./plugins/share-count');
      require('./plugins/smart-resize');
      // require('./plugins/stroke');

      // require('owl.carousel');


      var Util = require('./modules/util');
      var Theme = require('./modules/theme');
      var Maps = require('./modules/maps');

      // var Map   		= require('./modules/map');
      // var Form   		= require('./modules/form');
      // var VideoManager   	= require('./modules/video-manager');

      jQuery(document).ready(function ($) {

        Util.detectBrowser(); // Adds Browser Version and OS as a class to HTML
        Theme.init(); // Start the Theme
        Maps.init();

        // let videoManager = new VideoManager($('.video-overlay'), $('.js-video-play'));
        // videoManager.init(Util);


        // // Init Maps
        // if ($('.g-map').length > 0) {
        // 	$('.g-map').each((i, el) => {

        // 		// create map
        // 		let map = new Map($(el));

        // 	});
        // }


        // if ($('.form').length > 0) {
        // 	$('.form').each((i, el) => {

        // 		// create map
        // 		let form = new Form($(el));

        // 	});
        // }
      });
    }).call(this, require("oMfpAn"), typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {}, require("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/fake_cf3badaf.js", "/");
  }, { "./modules/maps": 6, "./modules/theme": 7, "./modules/util": 8, "./plugins/share": 10, "./plugins/smart-resize": 11, "buffer": 1, "oMfpAn": 4 }], 6: [function (require, module, exports) {
    (function (process, global, Buffer, __argument0, __argument1, __argument2, __argument3, __filename, __dirname) {
      // ------------------------------------
      //
      // Maps Module
      //
      // ------------------------------------


      (function ($) {

        if (typeof window.Map == 'undefined') window.Map = {};

        Map = {
          lat: -27.9210555,
          lng: 133.247866,
          // lat: -42.181723,
          // long: 156.181641,
          markers: [],
          active_marker: null,
          map: null,
          infoWindows: [],
          park: false,

          // ------------------------------------
          // Init
          // ------------------------------------

          init: function init(location) {

            if ($('.map').length <= 0) {
              return;
            }

            console.log("Map::init");

            this.options = {
              zoom: 5, // How zoomed in you want the map to start at (always required)
              center: new google.maps.LatLng(this.lat, this.lng),
              zoomControl: false,
              disableDoubleClickZoom: false,
              mapTypeControl: false,
              scaleControl: false,
              scrollwheel: false,
              panControl: false,
              streetViewControl: false,
              draggable: true,
              overviewMapControl: true,
              overviewMapControlOptions: {
                opened: false
              },
              // styles : [{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"visibility":"off"},{"color":"#333333"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"off"},{"color":"#ffffff"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]},{"featureType":"administrative.country","elementType":"geometry.fill","stylers":[{"visibility":"off"},{"saturation":"13"}]},{"featureType":"administrative.country","elementType":"geometry.stroke","stylers":[{"color":"#dc2323"},{"visibility":"off"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"landscape.natural.landcover","elementType":"geometry.fill","stylers":[{"color":"#fffefd"}]},{"featureType":"landscape.natural.landcover","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"landscape.natural.landcover","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"featureType":"road","elementType":"all","stylers":[{"visibility":"on"},{"weight": "2.00"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"transit.line","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"transit.station.airport","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"transit.station.bus","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"transit.station.rail","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#e1e1e1"}]}]
              styles: [{ "featureType": "water", "elementType": "geometry.fill", "stylers": [{ "color": "#009dd1" }] }, { "featureType": "landscape", "elementType": "geometry", "stylers": [{ "color": "#efefef" }] }, { "featureType": "road.highway", "elementType": "geometry.fill", "stylers": [{ "color": "#fbb900" }] }, { "featureType": "road.highway", "elementType": "geometry.stroke", "stylers": [{ "color": "#efc964" }, { "weight": 0.2 }] }, { "featureType": "road.arterial", "elementType": "geometry", "stylers": [{ "color": "#ffffff" }, { "lightness": 18 }] }, { "featureType": "road.local", "elementType": "geometry", "stylers": [{ "color": "#ffffff" }, { "lightness": 16 }] }, { "featureType": "poi", "elementType": "geometry", "stylers": [{ "color": "#f5f5f5" }, { "lightness": 21 }] }, { "featureType": "poi.park", "elementType": "geometry", "stylers": [{ "color": "#e0eadc" }] }, { "elementType": "labels.text.stroke", "stylers": [{ "visibility": "on" }, { "color": "#ffffff" }, { "lightness": 16 }] }, { "elementType": "labels.text.fill", "stylers": [{ "saturation": 36 }, { "color": "#333333" }, { "lightness": 15 }] }, { "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] }, { "featureType": "transit", "elementType": "geometry", "stylers": [{ "visibility": "off" }] }, { "featureType": "administrative", "elementType": "geometry.fill", "stylers": [{ "color": "#fefefe" }, { "lightness": 20 }] }, { "featureType": "administrative", "elementType": "geometry.stroke", "stylers": [{ "color": "#A2B0B0" }, { "weight": 1.7 }] }

              // {"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#cbe9ff"}]},
              // {"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#e5e5e5"},{"lightness":20}]},
              // {"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},
              // {"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#d4e0e0"},{"weight":0.2}]},
              // {"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},
              // {"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},
              // {"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},
              // {"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},
              // {"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},
              // {"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#A2B0B0"},{"weight":1.7}]}
              ]
            };

            if (location) {
              this.options.zoom = 12;
              this.options.center = new google.maps.LatLng(location.lat, location.lng);
            }

            // Set Center To Austalia
            // Map.options.center = new google.maps.LatLng(Map.lat, Map.lng-6);

            var map_container = jQuery('#map');
            // var type = map_container.attr('data-mapType');
            var type = map_container.data('mapType') ? map_container.data('mapType') : map_container.attr('data-mapType');

            if (type == 'locations') {
              this.options.zoomControl = true;
              google.maps.event.addDomListener(window, 'load', Map.locations);
            } else if (type == 'simple') {} else if (type == 'read') {
              google.maps.event.addDomListener(window, 'load', Map.read);
            } else if (type == 'read-single') {

              console.log('MAP TYPE:: read-single');

              this.options.zoom = 14;

              // this.park = true;
              this.park = false;

              google.maps.event.addDomListener(window, 'load', Map.read);
            }
          },

          // ------------------------------------
          // Simple
          // ------------------------------------

          simple: function simple() {

            var map_container = jQuery('#map');

            // Get the HTML DOM element that will contain your map
            var mapElement = document.getElementById('map');

            // Map.options.zoom = 3;

            // Create the Google Map using our element and options defined above
            Map.map = new google.maps.Map(mapElement, Map.options);

            // Plot Locations
            // var locations = Map.readLocations();
          },

          // ------------------------------------
          // Read
          // ------------------------------------

          read: function read() {

            var map_container = jQuery('#map');

            // Get the HTML DOM element that will contain your map
            var mapElement = document.getElementById('map');

            // Map.options.zoom = 3;

            // Create the Google Map using our element and options defined above
            Map.map = new google.maps.Map(mapElement, Map.options);

            // Plot Locations
            var locations = Map.readLocations();
          },

          // ------------------------------------
          // Read Locations
          // ------------------------------------

          readLocations: function readLocations() {

            $('[data-lat]').each(function (e) {

              // offset
              var offset = $(this).attr('data-offset');

              if (offset) {

                var lat = $(this).attr('data-lat');
                var lng = $(this).attr('data-lng') - 0.03;

                var center = new google.maps.LatLng(lat, lng);

                Map.map.panTo(center);
              } else {
                var marker = new google.maps.Marker({
                  position: new google.maps.LatLng($(this).attr('data-lat'), $(this).attr('data-lng')),
                  map: Map.map,
                  animation: google.maps.Animation.DROP,
                  icon: '/wp-content/themes/cpp/assets/dist/img/map-marker.png'
                });

                marker.addListener('click', function () {

                  $('html, body').animate({
                    scrollTop: $('[data-lat="' + this.position.lat() + '"]').offset().top
                  }, 500);
                });
              }

              if (Map.park == true) {

                var center = new google.maps.LatLng($(this).attr('data-lat'), $(this).attr('data-lng'));
                Map.map.panTo(center);
              }
            });
          },

          // ------------------------------------
          // Locations
          // ------------------------------------

          locations: function locations() {

            var map_container = jQuery('#map');

            // Get the HTML DOM element that will contain your map
            var mapElement = document.getElementById('map');

            // Map.options.zoom = 3;

            // Create the Google Map using our element and options defined above
            Map.map = new google.maps.Map(mapElement, Map.options);

            // Plot Locations
            var locations = Map.plotLocations();
          },

          // ------------------------------------
          // Get Parks
          // ------------------------------------

          plotLocations: function plotLocations() {

            $.get('/api/locations', function (data) {

              for (var i in data) {

                // var m = {};
                // m.title     = data[i].title;
                // m.content   = data[i].description;
                // m.lat       = data[i].location.lat;
                // m.lng       = data[i].location.lng;
                // m.partner_id= data[i].partner_id;
                // m.link      = data[i].link;


                // CUSTOM: only add partners
                if (data[i].partner_id) {

                  // New Window
                  var infoWindow = new google.maps.InfoWindow({
                    content: "<div class=\"info-window\">\n                                            <div class=\"wrap\">\n                                                <h3>" + data[i].title + "</h3>\n                                                <p>" + data[i].description + "</p>\n                                                <a href=\"" + data[i].link + "\">Learn More</a>\n                                            </div>\n                                      </div>"
                  });

                  Map.infoWindows[i] = infoWindow;

                  var icon = '/wp-content/themes/cpp/assets/dist/img/map-marker.png';

                  // // New Marker
                  var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(data[i].location.lat, data[i].location.lng),
                    map: Map.map,
                    title: data[i].title,
                    excerpt: data[i].content,
                    animation: google.maps.Animation.DROP,
                    icon: icon,
                    link: data[i].link,
                    info: infoWindow
                  });

                  marker.addListener('click', function () {
                    $('.google-info-window').removeClass('active');
                    for (var w in Map.infoWindows) {
                      Map.infoWindows[w].close();
                    }

                    this.info.open(Map.map, this);
                  });

                  google.maps.event.addListener(infoWindow, 'domready', function () {
                    var l = $('.info-window').parent().parent().parent().parent().addClass('google-info-window');
                    setTimeout(function () {
                      l.addClass('active');
                    }, 200);

                    // for (var i = 0; i < l.length; i++) {
                    //     if($(l[i]).css('z-index') == 'auto') {
                    //         $(l[i]).css('border-radius', '16px 16px 16px 16px');
                    //         $(l[i]).css('border', '2px solid red');
                    //     }
                    // }
                  });
                }
              }
            });
          },

          // ------------------------------------
          // Add Marker
          // ------------------------------------

          add_marker: function add_marker(data, click_event) {
            var marker = new google.maps.Marker({
              position: new google.maps.LatLng(data.lat, data.lng),
              map: Map.map,
              title: data.title,
              excerpt: data.content,
              animation: google.maps.Animation.DROP,
              icon: '/wp-content/themes/cpp/assets/dist/img/map-marker.png',
              link: data.link
            });

            if (click_event) {
              marker.addListener('click', click_event);
            }

            Map.markers.push(marker);

            return marker;
          },

          // ------------------------------------
          // Clear Markers
          // ------------------------------------

          clear_markers: function clear_markers(map) {
            for (var i = 0; i < Map.markers.length; i++) {
              Map.markers[i].setMap(map);
            }
            Map.markers = [];
          },

          // ------------------------------------
          // Marker Click
          // ------------------------------------

          marker_click: function marker_click() {

            window.location = this.link;
            return;
          },

          // ------------------------------------
          // Location Click
          // ------------------------------------

          locationClick: function locationClick() {

            var marker = this;

            // Set Content
            var $title = $('.map-row .need-to-know .title');
            var $content = $('.map-row .need-to-know .text');
            console.log(marker.excerpt);

            setTimeout(function () {
              $title.fadeOut();
            }, 1);
            setTimeout(function () {
              $content.fadeOut();
            }, 1);
            setTimeout(function () {
              $title.text(decodeURIComponent(marker.title).replace(/\+/g, " "));
            }, 500);
            setTimeout(function () {
              $content.html(decodeURIComponent(marker.excerpt).replace(/\+/g, " "));
            }, 500);
            setTimeout(function () {
              $title.fadeIn();
            }, 700);
            setTimeout(function () {
              $content.fadeIn();
            }, 800);

            // Fade in
            $('.map-row .need-to-know').addClass('active');
          }

        };

        module.exports = Map;
      })(jQuery);
    }).call(this, require("oMfpAn"), typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {}, require("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/modules/maps.js", "/modules");
  }, { "buffer": 1, "oMfpAn": 4 }], 7: [function (require, module, exports) {
    (function (process, global, Buffer, __argument0, __argument1, __argument2, __argument3, __filename, __dirname) {
      // ------------------------------------
      //
      // Theme
      //
      // ------------------------------------

      var util = require('./util.js');
      require('./../plugins/count-to.js');

      (function ($) {

        if (typeof window.Theme == 'undefined') window.Theme = {};

        Theme = {

          $nav: $('.nav-main'),
          $navToggle: $('#nav-toggle'),
          $jsFooter: $('.js-footer-cols'),

          settings: {},

          // ----------------------------
          // Theme Init
          // ----------------------------
          init: function init() {
            var _this = this;

            // Share Links
            this.shareLinks();

            // Add Placeholder Support
            this.placeholders();

            // Format Number
            this.formatNumbers();

            // Equalize height
            this.equalizeHeight();

            $(window).smartresize(function () {
              _this.equalizeHeight();
            });

            // Mobile menu
            this.mobileMenu();

            // Footer wrap
            this.footerWrap();

            // Councils
            this.partners();

            // Layout
            this.layout();

            // Forms
            this.forms();

            // Donate
            this.donate();

            // Load desktop and mobile only scripts
            if (isMobile()) {} else {

              this.scrollMagic();
            }
          },

          // ----------------------------
          // js layout
          // ----------------------------
          layout: function layout() {
            if ($('.bg-extend').length > 0) {

              var elem = $('.bg-extend'),
                  padd = 20,
                  maxW = 1200;

              setBlockWidth(elem, padd, maxW);

              $(window).on('resize', function () {

                setBlockWidth(elem, padd, maxW);
              });
            }
          },


          // ----------------------------
          // Forms
          // ----------------------------
          forms: function forms() {

            $(document).delegate('.submit-wrap', 'click', function (e) {
              e.preventDefault();

              console.log('delegate -- clicked on submit wrap');

              $(this).find('input[type=button]').click();
            });

            $('.submit-wrap').on('click', function (e) {

              e.preventDefault();

              console.log('clikced on submit wrap');
            });
          },


          // ----------------------------
          // Mobile Menu
          // ----------------------------
          mobileMenu: function mobileMenu() {

            $('#nav-toggle').on('click', function (e) {

              e.preventDefault();

              $(this).toggleClass('-x');
              Theme.$nav.toggleClass('-open');
            });
          },


          // ----------------------------
          // 
          // ----------------------------
          footerWrap: function footerWrap() {

            if (!Theme.$jsFooter.length > 0) return;

            if (!$('.js-wrap-left').length > 0) return;
            $('.js-wrap-left').css('opacity', '0');
            $('.js-wrap-left').wrapAll('<div class="col -left"></div>');
            $('.js-wrap-left').css('opacity', '1');

            if (!$('.js-wrap-mid').length > 0) return;
            $('.js-wrap-mid').css('opacity', '0');
            $('.js-wrap-mid').wrapAll('<div class="col -mid"></div>');
            $('.js-wrap-mid').css('opacity', '1');

            if (!$('.js-wrap-right').length > 0) return;
            $('.js-wrap-right').css('opacity', '0');
            $('.js-wrap-right').wrapAll('<div class="col -right"></div>');
            $('.js-wrap-right').css('opacity', '1');
          },


          // ----------------------------
          // Equalize Height
          // ----------------------------
          equalizeHeight: function equalizeHeight() {
            // if (util.isMobile()) return;
            if ($(window).width() < 736) return;

            if ($('[data-equal-heights]').length < 1) return;

            function setEqualHeights() {
              $('[data-equal-heights]').each(function () {

                // _this.setHeights();

                var eqSelectors = $(this).data('equal-heights'),
                    $eqSelectors = $(this).find(eqSelectors),
                    mobile = $(this).data('equal-mobile') ? true : false;

                // Cache the highest
                var highestBox = 0;

                if ($(window).width() < 768 && mobile != true) {

                  $($eqSelectors, this).each(function () {
                    $(this).css('height', '');
                  });
                } else {

                  // Select and loop the elements you want to equalise
                  $($eqSelectors, this).each(function () {

                    // Remove height if set from before to account for resize
                    $(this).css('height', '');

                    // If this box is higher than the cached highest then store it
                    if ($(this).height() > highestBox) {
                      highestBox = $(this).height();
                    }
                  });

                  // Set the height of all those children to whichever was highest 
                  $($eqSelectors, this).height(highestBox);
                }
              });
            }

            setEqualHeights();

            $(window).on('resize', function () {

              setEqualHeights();
            });

            // $(window).resize($.throttle(1000, setEqualHeights));
          },


          // ----------------------------
          // Share Links
          // ----------------------------
          shareLinks: function shareLinks() {

            $("a[data-share]").share({
              counts: false
            });

            $("[data-shareCount]").each(function () {
              $(this).shareCount();
            }); //shareCount();
          },

          // ----------------------------
          // Placeholders
          // ----------------------------
          placeholders: function placeholders() {

            // $('input, textarea').placeholder();

          },

          // ----------------------------
          // Councils
          // ----------------------------
          partners: function partners() {

            // Hero width
            // ----------------------------
            if ($('.js-bg-solid').length > 0) {

              var elem = $('.js-bg-solid'),
                  padd = 20,
                  maxW = 960;

              setBlockWidth(elem, padd, maxW);

              $(window).on('resize', function () {

                setBlockWidth(elem, padd, maxW);
              });
            }

            // Postcodes
            // ----------------------------
            $('#postcode-redirect').on('click', function (e) {
              e.preventDefault();

              var $form = $(this).parent().parent('form'),
                  $results = $form.find('.form-results'),
                  $redirect = $form.find('#postcode-redirect-url'),
                  postcode = $('input#postcode').val(),
                  loading = $('<div class="loading-dots"><span></span><span></span><span></span></div>'),
                  text = $(this).data('button-text') ? $(this).data('button-text') : 'Submit';

              $(this).addClass('-disabled');
              $(this).html(loading);

              if (postcode && postcode.length === 4) {

                $.get('/api/locations/search?postcode=' + postcode, function (data) {

                  if (data && data[0]['partner_id'] != false) {

                    var partner = data[0]['link'];

                    if (partner) {
                      $form.attr('action', data[0]['link']);
                    } else {
                      $form.attr('action', '/nominate');
                    }

                    $form.submit();
                  } else {
                    console.log('Postcode not found');

                    $form.attr('action', '/pending');
                    $form.submit();
                  }
                });
              } else {

                setTimeout(function () {

                  $error.html('Please enter a valid postcode.');

                  $('#postcode-submit').removeClass('-disabled');
                  $('#postcode-submit').html(text);
                }, 500);
              }
            });

            // NOMINATE PAGE
            $('#postcode-lookup').on('click', function (e) {
              e.preventDefault();

              var $form = $(this).parent().parent('form'),
                  $container = $form.parent('.aside-nominate'),
                  $results = $form.find('.form-results-wrapper'),
                  $redirect = $form.find('#postcode-redirect-url'),
                  postcode = $('input#postcode').val(),
                  loading = $('<div class="loading-dots"><span></span><span></span><span></span></div>');

              // Hidden form fields
              var $hidden_postcode = $('input#nf-field-9'),
                  $hidden_community = $('input#nf-field-14');

              $(this).addClass('-disabled');
              $results.find('.loading-dots-wrapper').html(loading);
              $container.removeClassPrefix('-response');
              $hidden_postcode.val(postcode);

              if (postcode && postcode.length === 4) {

                $.get('/api/locations/search?postcode=' + postcode, function (data) {

                  if (data && data[0]['partner_id'] != false) {

                    var partner = data[0]['link'];

                    if (partner) {
                      $redirect.attr('href', partner);
                    }

                    setTimeout(function () {

                      $container.addClass('-response-partner-exists');
                      $results.find('.loading-dots-wrapper').html('');
                    }, 500);
                  } else {

                    if (data) {

                      var community = data[0]['title'];

                      $('input#nf-field-14').val(community);
                    }

                    setTimeout(function () {

                      $container.addClass('-response-partner-pending');
                      $results.find('.loading-dots-wrapper').html('');
                    }, 500);
                  }
                });
              } else {
                setTimeout(function () {
                  $results.find('.loading-dots-wrapper').html('');
                  $container.addClass('-response-error');
                  $('#postcode-lookup').removeClass('-disabled');
                }, 500);
              }
            });

            $('input#postcode').on('focus', function (e) {

              var $form = $(this).parent('form'),
                  $button = $form.find('.postcode-submit'),
                  text = $button.data('button-text') ? $button.data('button-text') : 'Submit',
                  $error = $form.find('.form-errors');

              if ($button.hasClass('-disabled')) {
                if ($button.hasClass('-animate-loading')) {
                  $button.removeClass('-disabled');
                  $button.html(text);
                  $error.html('');
                } else {
                  $button.removeClass('-disabled');
                }
              }
            });

            // If postcode exists on nominate page
            // ----------------------------
            if ($('.aside-nominate').length > 0) {

              if ($.urlParam('postcode') != '') {
                // variable_name would be the name of your variable within your url following the ? symbol
                //execute if empty

                console.log('postcode param exists');

                $('#postcode-lookup').click();
              } else {}
              // execute if there is a variable


              // if ($.urlParam('postcode') != 0) {

              //   console.log('postcode param exists')

              //   // $('#postcode-lookup').click();

              // }
              // else {

              // }
            }
          },

          // ------------------------------------
          // Format numbers with comma
          // ------------------------------------

          formatNumbers: function formatNumbers() {

            $('.-format-number').each(function () {

              $(this).text(formatNumber($(this).text()));
            });

            if (isMobile()) {

              $('.-scroll-counter').each(function () {

                $(this).text(formatNumber($(this).text()));
              });
            }
          },

          // ----------------------------
          // Scroll Magic
          // ----------------------------
          scrollMagic: function scrollMagic() {
            var controller = new ScrollMagic.Controller();

            // ------------------------------------
            // animate active section
            // ------------------------------------
            $('section').each(function () {

              var section_id = $(this).attr('id');
              var section_height = $(this).height();

              new $.ScrollMagic.Scene({
                duration: section_height,
                triggerElement: $(this)
              }).setClassToggle('#nav-' + section_id, '-active').addTo(controller);
            });

            $('.subsection-parent').each(function () {

              var subsection_id = $(this).attr('id');
              var subsection_height = $(this).height();

              new $.ScrollMagic.Scene({
                duration: subsection_height,
                triggerElement: $(this)
              }).setClassToggle('#nav-' + subsection_id, '-active').addTo(controller);
            });

            $('.-bg-change').each(function () {

              var bg_id = $(this).attr('id');
              var bg_height = $(this).height();

              new $.ScrollMagic.Scene({
                duration: 0,
                triggerElement: $(this).data('change'),
                triggerHook: 'onLeave'
              }).setClassToggle('body', '-' + bg_id).addTo(controller);
            });

            // ------------------------------------
            // animate zoomIn
            // ------------------------------------
            $('.-scroll-zoomIn').each(function () {

              $(this).addClass('-inactive');

              new $.ScrollMagic.Scene({
                duration: '100%',
                triggerElement: $(this).parents('.subsection')

              }).setClassToggle($(this), '-active animated zoomIn').addTo(controller);
            });

            // ------------------------------------
            // animate fadeOut
            // ------------------------------------
            $('.-scroll-fadeOut').each(function () {

              new $.ScrollMagic.Scene({
                duration: '100%',
                triggerElement: $(this),
                triggerHook: 'onLeave'
              }).setClassToggle($(this), 'animated fadeOut').addTo(controller);
            });

            // ------------------------------------
            // animate fadeInUp
            // ------------------------------------
            $('.-scroll-fadeInUp').each(function () {

              $(this).addClass('-inactive');

              new $.ScrollMagic.Scene({
                duration: 0,
                triggerElement: $(this).parents('[data-scroll-trigger]')
              }).setClassToggle($(this), '-active animated fadeInUp').addTo(controller);
            });

            // ------------------------------------
            // animate fadeOutUp
            // ------------------------------------
            $('.-scroll-fadeOutUp').each(function () {

              new $.ScrollMagic.Scene({
                duration: '100%',
                triggerElement: $(this).parents('.subsection'),
                triggerHook: 'onLeave'
              }).setClassToggle($(this), 'animated fadeOutUp').addTo(controller);
            });

            // ------------------------------------
            // animate parallax
            // ------------------------------------
            $('.-scroll-parallax').each(function () {

              var $fast_layers = $(this).find('.layer-fast');
              var $slow_layers = $(this).find('.layer-slow');
              var $slower_layers = $(this).find('.layer-slower');

              // build neonate
              var neonate = new TimelineMax().add([TweenMax.fromTo($fast_layers, 1, { top: 250 }, { top: -150, ease: Linear.easeNone }), TweenMax.fromTo($slow_layers, 1, { top: 150 }, { top: -50, ease: Linear.easeNone }), TweenMax.fromTo($slower_layers, 1, { top: 50 }, { top: 0, ease: Linear.easeNone })]);

              // create scene
              new $.ScrollMagic.Scene({
                duration: '100%',
                triggerElement: $(this)
              }).setTween(neonate).addTo(controller);
            });

            // ------------------------------------
            // animate counter
            // ------------------------------------
            $('.-scroll-counter').each(function () {

              new $.ScrollMagic.Scene({
                duration: 0,
                triggerElement: $(this),
                triggerHook: 'onEnter'
              }).on('start', function () {

                var element = this.triggerElement();

                var $counter = $('#' + element.id);

                var total = $counter.data('total');
                var duration = $counter.data('duration');
                var multiplier = total > 1000000 ? 1000 : 1;

                // animate value from x to y:
                $({ someValue: 0 }).animate({ someValue: total }, {
                  duration: duration,
                  easing: 'swing',
                  step: function step() {
                    // update text with rounded-up value
                    $counter.text(formatNumber(Math.ceil(this.someValue / multiplier) * multiplier));
                  }
                });
              }).addTo(controller);
            });
          },

          // ----------------------------
          // Donate
          // ----------------------------
          donate: function donate() {
            Payments.form.on('PAYMENTS_FORM_SCREEN_NEXT', function (element) {
              var $form = $('.payments-form'),
                  active = $form.find('.screen.active').index();

              $form.removeClassPrefix('-step-');
              $form.addClass('-step-' + active);
            });

            Payments.form.on('PAYMENTS_FORM_SCREEN_PREV', function (element) {
              var $form = $('.payments-form'),
                  active = $form.find('.screen.active').index();

              $form.removeClassPrefix('-step-');
              $form.addClass('-step-' + active);
            });
          }
        };

        module.exports = Theme;

        /**
         * Remove Class Prefix
         * 
         * @param  {String} prefix 
         */
        $.fn.removeClassPrefix = function (prefix) {
          this.each(function (i, el) {
            var classes = el.className.split(" ").filter(function (c) {
              return c.lastIndexOf(prefix, 0) !== 0;
            });
            el.className = $.trim(classes.join(" "));
          });
          return this;
        };

        function setBlockWidth(elem, padd, maxW) {
          var winW = $(window).width(),
              diffW = (winW - maxW) / 2 + 10;

          if (winW > maxW) {
            elem.css('width', diffW);

            if (elem.hasClass('-pos-left')) elem.css('left', '-' + diffW + 'px');
          } else {

            // if( winW < 736 ) {
            //   elem.css('width','')
            // }
            // else {

            if (elem.hasClass('-mobile-full') && winW < 736) {
              elem.css('width', '100%');
            } else {
              elem.css('width', padd);

              if (elem.hasClass('-pos-left')) elem.css('left', '-' + padd + 'px');
            }

            // }
          }
        }

        function formatNumber(val) {
          while (/(\d+)(\d{3})/.test(val.toString())) {
            val = val.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
          }
          return val;
        }

        function isMobile() {
          return $(window).width() < 769 ? true : false;
        }

        $.urlParam = function (name) {
          var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);

          if (results == null) return false;
          return results[1] || 0;
        };
      })(jQuery);
    }).call(this, require("oMfpAn"), typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {}, require("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/modules/theme.js", "/modules");
  }, { "./../plugins/count-to.js": 9, "./util.js": 8, "buffer": 1, "oMfpAn": 4 }], 8: [function (require, module, exports) {
    (function (process, global, Buffer, __argument0, __argument1, __argument2, __argument3, __filename, __dirname) {
      // ------------------------------------
      //
      // Utilities
      //
      // ------------------------------------

      (function ($) {

        if (typeof window.Util == 'undefined') window.Util = {};

        Util = {

          // ------------------------------------
          // Util Init
          // ------------------------------------

          init: function init() {

            console.log('Util::init()');
          },

          isMobile: function isMobile() {
            return $(window).width() < 768;
          },

          isTablet: function isTablet() {
            return $(window).width() < 1024;
          },
          // ------------------------------------
          // CSS Helpers
          // ------------------------------------

          detectBrowser: function detectBrowser() {

            if (!$.browser) return;

            // Firefox
            if ($.browser.mozilla) {
              $('html').addClass('firefox');
            }

            // Chrome
            if ($.browser.chrome) {
              $('html').addClass('chrome');
            }

            // Safari
            if ($.browser.safari) {
              $('html').addClass('safari');
            }

            // IE
            if ($.browser.msie) {
              $('html').addClass('ie');
            }
            if (!!navigator.userAgent.match(/Trident\/7\./)) {
              $('html').addClass('ie');
            }

            // OS
            var os = window.navigator.platform.toLowerCase();

            // Windows
            if (os.indexOf('win') >= 0) {
              $('html').addClass('windows');
            } else if (os.indexOf('mac') >= 0) {
              $('html').addClass('mac');
            }

            // IOS
            if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
              $('html').addClass('ios');
            }

            // IE Version
            var ua = window.navigator.userAgent;
            var msie = ua.indexOf('MSIE ');
            if (msie > 0) {
              // IE 10 or older => return version number
              var version = parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
              $('html').addClass('ie' + version);
            }

            var trident = ua.indexOf('Trident/');
            if (trident > 0) {
              // IE 11 => return version number
              var rv = ua.indexOf('rv:');
              var version = parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
              $('html').addClass('ie' + version);
            }

            var edge = ua.indexOf('Edge/');
            if (edge > 0) {
              // Edge (IE 12+) => return version number
              var version = parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
              $('html').addClass('ie' + version);
            }
          }

        };

        module.exports = Util;
      })(jQuery);
    }).call(this, require("oMfpAn"), typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {}, require("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/modules/util.js", "/modules");
  }, { "buffer": 1, "oMfpAn": 4 }], 9: [function (require, module, exports) {
    (function (process, global, Buffer, __argument0, __argument1, __argument2, __argument3, __filename, __dirname) {
      (function ($) {
        $.fn.countTo = function (options) {
          options = options || {};

          return $(this).each(function () {
            // set options for current element
            var settings = $.extend({}, $.fn.countTo.defaults, {
              from: $(this).data('from'),
              to: $(this).data('to'),
              speed: $(this).data('speed'),
              refreshInterval: $(this).data('refresh-interval'),
              decimals: $(this).data('decimals')
            }, options);

            // how many times to update the value, and how much to increment the value on each update
            var loops = Math.ceil(settings.speed / settings.refreshInterval),
                increment = (settings.to - settings.from) / loops;

            // references & variables that will change with each update
            var self = this,
                $self = $(this),
                loopCount = 0,
                value = settings.from,
                data = $self.data('countTo') || {};

            $self.data('countTo', data);

            // if an existing interval can be found, clear it first
            if (data.interval) {
              clearInterval(data.interval);
            }
            data.interval = setInterval(updateTimer, settings.refreshInterval);

            // initialize the element with the starting value
            render(value);

            function updateTimer() {
              value += increment;
              loopCount++;

              render(value);

              if (typeof settings.onUpdate == 'function') {
                settings.onUpdate.call(self, value);
              }

              if (loopCount >= loops) {
                // remove the interval
                $self.removeData('countTo');
                clearInterval(data.interval);
                value = settings.to;

                if (typeof settings.onComplete == 'function') {
                  settings.onComplete.call(self, value);
                }
              }
            }

            function render(value) {
              var formattedValue = settings.formatter.call(self, value, settings);
              $self.html(formattedValue);
            }
          });
        };

        $.fn.countTo.defaults = {
          from: 0, // the number the element should start at
          to: 0, // the number the element should end at
          speed: 1000, // how long it should take to count between the target numbers
          refreshInterval: 100, // how often the element should be updated
          decimals: 0, // the number of decimal places to show
          formatter: formatter, // handler for formatting the value before rendering
          onUpdate: null, // callback method for every time the element is updated
          onComplete: null // callback method for when the element finishes updating
        };

        function formatter(value, settings) {
          return value.toFixed(settings.decimals);
        }
      })(jQuery);
    }).call(this, require("oMfpAn"), typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {}, require("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/plugins/count-to.js", "/plugins");
  }, { "buffer": 1, "oMfpAn": 4 }], 10: [function (require, module, exports) {
    (function (process, global, Buffer, __argument0, __argument1, __argument2, __argument3, __filename, __dirname) {
      (function ($) {

        "use strict";

        $.fn.share = function (options) {

          var defaults = {
            threshold: 0,
            abbreviate: true,
            counts: false
          };

          var $this = this,
              settings = $.extend(defaults, options);

          var init = function init($element) {

            var options = {
              url: typeof $element.data('url') !== 'undefined' ? $element.data('url') : document.URL,
              message: typeof $element.data('message') !== 'undefined' ? $element.data('message') : false,
              via: typeof $element.data('via') !== 'undefined' ? $element.data('via') : false,
              popup: typeof $element.data('popup') !== 'undefined' ? $element.data('popup') : true,
              count: typeof $element.data('count') !== 'undefined' ? $element.data('count') : settings.counts,
              network: typeof $element.data('share') !== 'undefined' ? $element.data('share') : "facebook"
            };

            if (options.count) var $count = $('<span class="js-share-count"/>');

            $element.addClass("js-share");

            switch (options.network) {

              case 'facebook':

                var params = {
                  u: options.url
                };
                var url = 'http://www.facebook.com/sharer/sharer.php?' + parameters(params);

                $element.addClass("js-share-facebook").attr("href", url);

                if (options.count) {
                  $.getJSON("http://graph.facebook.com/" + options.url, function (data) {
                    if (data.shares > settings.threshold) $count.text(abbreviate(data.shares)).removeClass('js-share-count-loading').appendTo($element);
                  });
                }

                break;

              case 'twitter':

                var params = {
                  via: options.via ? options.via : "",
                  text: options.message ? options.message : "",
                  url: options.url
                };
                var url = 'https://twitter.com/intent/tweet?' + parameters(params);

                $element.addClass("js-share-twitter").attr("href", url);

                if (options.count) {
                  $.getJSON("http://urls.api.twitter.com/1/urls/count.json?url=" + options.url + "&callback=?", function (data) {
                    if (data.count > settings.threshold) $count.text(abbreviate(data.count)).removeClass('js-share-count-loading').appendTo($element);
                  });
                }

                break;

            }

            if (options.popup) {

              $element.attr({
                rel: "external",
                target: "_blank"
              });

              $element.click(function (event) {
                open($(this).attr("href"));
                return false;
              });
            }
          };

          var abbreviate = function abbreviate(number) {

            if (!settings.abbreviate) return number;

            number = number.toString();

            if (number.length > 6) return number.substr(0, 1) + "." + number.substr(1, 1) + "m";else if (number.length > 5) return number.substr(0, 3) + "k";else if (number.length > 5) return number.substr(0, 3) + "k";else if (number.length > 4) return number.substr(0, 2) + "k";else if (number.length > 3) return number.substr(0, 1) + "." + number.substr(1, 1) + "k";else return number;
          };

          var open = function open(url, w, h) {
            var w = typeof w !== 'undefined' ? w : 600,
                h = typeof h !== 'undefined' ? h : 300,
                left = screen.width / 2 - w / 2,
                top = screen.height / 2 - h / 2;
            window.open(url, "MsgWindow", 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
          };

          var parameters = function parameters(obj) {
            if (!Object.keys) {
              Object.keys = function (obj) {
                var keys = [];
                for (var i in obj) {
                  if (obj.hasOwnProperty(i)) {
                    keys.push(i);
                  }
                }
                return keys;
              };
            }
            return $.map(Object.keys(obj), function (key) {
              if (!obj[key]) return "";
              return encodeURIComponent(key) + '=' + encodeURIComponent(obj[key]);
            }).join('&');
          };

          return this.each(function () {

            init($(this));
          });
        };
      })(jQuery);
    }).call(this, require("oMfpAn"), typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {}, require("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/plugins/share.js", "/plugins");
  }, { "buffer": 1, "oMfpAn": 4 }], 11: [function (require, module, exports) {
    (function (process, global, Buffer, __argument0, __argument1, __argument2, __argument3, __filename, __dirname) {
      // ------------------------------------
      //
      // Smart Resize
      //
      // ------------------------------------

      (function ($, sr) {

        // debouncing function from John Hann
        // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
        var debounce = function debounce(func, threshold, execAsap) {
          var timeout;

          return function debounced() {
            var obj = this,
                args = arguments;
            function delayed() {
              if (!execAsap) func.apply(obj, args);
              timeout = null;
            };

            if (timeout) clearTimeout(timeout);else if (execAsap) func.apply(obj, args);

            timeout = setTimeout(delayed, threshold || 100);
          };
        };
        // smartresize 
        jQuery.fn[sr] = function (fn) {
          return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr);
        };
      })(jQuery, 'smartresize');
    }).call(this, require("oMfpAn"), typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {}, require("buffer").Buffer, arguments[3], arguments[4], arguments[5], arguments[6], "/plugins/smart-resize.js", "/plugins");
  }, { "buffer": 1, "oMfpAn": 4 }] }, {}, [5]);
//# sourceMappingURL=site.js.map
